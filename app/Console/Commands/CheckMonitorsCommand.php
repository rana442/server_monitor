<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Monitor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class CheckMonitorsCommand extends Command
{
    protected $signature = 'monitors:check';
    protected $description = 'Check all active monitors (HTTP, Ping, Port)';

    public function handle()
    {
        Log::info('=== MONITOR CHECK STARTED === ' . now());
        $this->info('[' . now() . '] Starting monitor checks...');
        
        $monitors = Monitor::where('is_active', true)->get();
        
        if ($monitors->isEmpty()) {
            $this->warn('No active monitors found.');
            Log::info('No active monitors to check.');
            return 0;
        }
        
        $this->info('Found ' . $monitors->count() . ' active monitor(s)');
        Log::info('Checking ' . $monitors->count() . ' monitor(s)');
        
        $checkedCount = 0;
        
        foreach ($monitors as $monitor) {
            $checkedCount++;
            $this->line("Checking {$checkedCount}/{$monitors->count()}: {$monitor->name} ({$monitor->type})");
            $this->checkMonitor($monitor);
        }

        $this->info('[' . now() . '] All monitors checked successfully.');
        Log::info('=== MONITOR CHECK COMPLETED === ' . now());
        
        return 0;
    }

    private function checkMonitor(Monitor $monitor)
    {
        $maxRetries = $monitor->retries ?? 1;
        $retryCount = 0;
        $result = null;
        
        do {
            $retryCount++;
            
            try {
                Log::info("Checking monitor (attempt {$retryCount}/{$maxRetries}): {$monitor->name} ({$monitor->type}: {$monitor->url})");
                
                $result = null;
                
                // Check based on monitor type
                switch ($monitor->type) {
                    case 'http':
                        $result = $this->checkHttp($monitor);
                        break;
                        
                    case 'ping':
                        $result = $this->checkPing($monitor);
                        break;
                        
                    case 'port':
                        $result = $this->checkPort($monitor);
                        break;
                        
                    default:
                        $result = [
                            'status' => false,
                            'response_time' => null,
                            'status_code' => null,
                            'error_message' => 'Unknown monitor type: ' . $monitor->type
                        ];
                }
                
                // If successful, break retry loop
                if ($result['status']) {
                    break;
                }
                
                // Wait before retry (if not last attempt)
                if ($retryCount < $maxRetries) {
                    sleep(1); // 1 second delay between retries
                }
                
            } catch (\Exception $e) {
                $result = [
                    'status' => false,
                    'response_time' => null,
                    'status_code' => null,
                    'error_message' => 'Exception: ' . $e->getMessage()
                ];
                
                if ($retryCount < $maxRetries) {
                    sleep(1);
                }
            }
            
        } while ($retryCount < $maxRetries && !$result['status']);
        
        // Process the final result
        $this->processMonitorResult($monitor, $result);
    }

    private function processMonitorResult(Monitor $monitor, array $result)
    {
        if ($result['status'] === null) {
            $result['status'] = false;
            $result['error_message'] = $result['error_message'] ?? 'Check failed after retries';
        }
        
        // Create log
        $monitor->pingLogs()->create([
            'status' => $result['status'],
            'response_time' => $result['response_time'],
            'status_code' => $result['status_code'],
            'error_message' => $result['error_message'] ?? null,
            'response_body' => $result['response_body'] ?? null,
        ]);

        // Update monitor status
        $monitor->last_checked_at = now();
        
        // Track status changes
        $oldStatus = $monitor->last_status;
        $monitor->last_status = $result['status'];
        
        if ($result['status'] && $oldStatus !== true) {
            $monitor->last_up_at = now();
            Log::info("Monitor UP: {$monitor->name} ({$monitor->type})");
        } elseif (!$result['status'] && $oldStatus !== false) {
            $monitor->last_down_at = now();
            Log::warning("Monitor DOWN: {$monitor->name} ({$monitor->type}) - " . ($result['error_message'] ?? 'No response'));
        }
        
        $monitor->save();
        
        // Calculate uptime
        $this->calculateUptime($monitor);
        
        $statusText = $result['status'] ? '🟢 UP' : '🔴 DOWN';
        $responseInfo = $result['response_time'] ? "({$result['response_time']}ms)" : '';
        $typeInfo = "({$monitor->type})";
        
        if ($result['status']) {
            $this->info("{$statusText} {$monitor->name} {$typeInfo} {$responseInfo}");
        } else {
            $this->error("{$statusText} {$monitor->name} {$typeInfo} - Error: " . ($result['error_message'] ?? 'Unknown error'));
        }
    }
    
    /**
     * Check HTTP/HTTPS URL
     */
    private function checkHttp(Monitor $monitor)
    {
        $startTime = microtime(true);
        
        try {
            $response = Http::timeout($monitor->timeout ?? 10)
                ->withOptions(['verify' => false])
                ->get($monitor->url);
                
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $status = $response->successful();
            $statusCode = $response->status();
            
            return [
                'status' => $status,
                'response_time' => $responseTime,
                'status_code' => $statusCode,
                'response_body' => $status ? null : substr($response->body(), 0, 500),
                'error_message' => $status ? null : "HTTP Error: {$statusCode}"
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => false,
                'response_time' => null,
                'status_code' => null,
                'error_message' => 'HTTP Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Check Ping (ICMP)
     */
    private function checkPing(Monitor $monitor)
    {
        $startTime = microtime(true);
        
        try {
            // Parse URL to get hostname
            $urlParts = parse_url($monitor->url);
            $host = $urlParts['host'] ?? str_replace(['http://', 'https://'], '', $monitor->url);
            
            // Remove port if present
            $host = explode(':', $host)[0];
            
            // Determine ping command based on OS
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            if ($isWindows) {
                // Windows ping command
                $command = "ping -n 1 -w " . (($monitor->timeout ?? 10) * 1000) . " $host";
                $successPattern = '/Received = 1/';
                $timePattern = '/Average = (\d+)ms/';
            } else {
                // Linux/Unix ping command
                $command = "ping -c 1 -W " . ($monitor->timeout ?? 10) . " $host";
                $successPattern = '/1 packets transmitted, 1 received/';
                $timePattern = '/time=([\d.]+) ms/';
            }
            
            // Execute ping command
            $process = Process::run($command);
            $output = $process->output();
            $error = $process->errorOutput();
            
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            if ($process->successful() && preg_match($successPattern, $output)) {
                // Extract response time
                $pingTime = null;
                if (preg_match($timePattern, $output, $matches)) {
                    $pingTime = floatval($matches[1]);
                }
                
                return [
                    'status' => true,
                    'response_time' => $pingTime ?? $responseTime,
                    'status_code' => 0,
                    'error_message' => null,
                    'response_body' => substr($output, 0, 500)
                ];
            } else {
                return [
                    'status' => false,
                    'response_time' => $responseTime,
                    'status_code' => 1,
                    'error_message' => 'Ping failed: ' . ($error ?: 'No response'),
                    'response_body' => substr($output . $error, 0, 500)
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'status' => false,
                'response_time' => null,
                'status_code' => null,
                'error_message' => 'Ping Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Check Port
     */
    private function checkPort(Monitor $monitor)
    {
        $startTime = microtime(true);
        
        try {
            // Parse URL to get host and port
            $urlParts = parse_url($monitor->url);
            
            // Extract host and port
            if (isset($urlParts['host'])) {
                $host = $urlParts['host'];
                $port = $urlParts['port'] ?? 80; // Default to port 80 if not specified
            } else {
                // Try to parse as host:port format
                $parts = explode(':', str_replace(['http://', 'https://'], '', $monitor->url));
                $host = $parts[0] ?? 'localhost';
                $port = $parts[1] ?? 80;
            }
            
            // Clean host (remove any path)
            $host = explode('/', $host)[0];
            
            // Use fsockopen for port checking
            $timeout = $monitor->timeout ?? 10;
            $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
            
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            if ($socket) {
                fclose($socket);
                return [
                    'status' => true,
                    'response_time' => $responseTime,
                    'status_code' => 0,
                    'error_message' => null,
                    'response_body' => "Port {$port} is open on {$host}"
                ];
            } else {
                return [
                    'status' => false,
                    'response_time' => $responseTime,
                    'status_code' => $errno,
                    'error_message' => "Port {$port} connection failed: {$errstr}",
                    'response_body' => null
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'status' => false,
                'response_time' => null,
                'status_code' => null,
                'error_message' => 'Port Check Error: ' . $e->getMessage()
            ];
        }
    }
    
    private function calculateUptime(Monitor $monitor)
    {
        // Calculate uptime based on last 100 logs
        $logs = $monitor->pingLogs()->latest()->limit(100)->get();
        
        if ($logs->count() > 0) {
            $upCount = $logs->where('status', true)->count();
            $uptimePercentage = ($upCount / $logs->count()) * 100;
            
            $monitor->uptime_percentage = round($uptimePercentage, 2);
            $monitor->save();
        }
    }
}