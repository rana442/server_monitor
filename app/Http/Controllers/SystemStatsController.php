<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemStatsController extends Controller
{
    /**
     * Get system statistics (CPU, Memory, Disk, etc.)
     */
    public function getStats()
    {
        // Cache for 5 seconds to prevent too frequent calculations
        return Cache::remember('system_stats', 5, function () {
            return [
                'cpu' => $this->getCpuUsage(),
                'memory' => $this->getMemoryUsage(),
                'disk' => $this->getDiskUsage(),
                'load' => $this->getSystemLoad(),
                'uptime' => $this->getUptime(),
                'timestamp' => now()->toDateTimeString(),
            ];
        });
    }
    
    /**
     * Get CPU usage percentage
     */
    private function getCpuUsage()
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                $output = [];
                exec('wmic cpu get loadpercentage', $output);
                $usage = (int) trim($output[1] ?? 0);
            } else {
                // Linux/Unix
                $load = sys_getloadavg();
                $usage = round($load[0] * 100 / (int) shell_exec('nproc'), 2);
                
                // Alternative method using /proc/stat
                $stat1 = file('/proc/stat');
                $info1 = explode(' ', preg_replace('!cpu +!', '', $stat1[0]));
                sleep(1);
                $stat2 = file('/proc/stat');
                $info2 = explode(' ', preg_replace('!cpu +!', '', $stat2[0]));
                
                $diff = array_map(function($a, $b) {
                    return $a - $b;
                }, $info2, $info1);
                
                $total = array_sum($diff);
                $idle = $diff[3] + $diff[4]; // idle + iowait
                $usage = round(100 * ($total - $idle) / $total, 2);
            }
            
            return [
                'percentage' => min(100, max(0, $usage)),
                'cores' => $this->getCpuCores(),
                'model' => $this->getCpuModel(),
            ];
            
        } catch (\Exception $e) {
            return [
                'percentage' => 0,
                'cores' => 'N/A',
                'model' => 'N/A',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get Memory usage
     */
    private function getMemoryUsage()
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                $output = [];
                exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value', $output);
                
                $data = [];
                foreach ($output as $line) {
                    if (strpos($line, '=') !== false) {
                        list($key, $value) = explode('=', $line);
                        $data[trim($key)] = trim($value);
                    }
                }
                
                $total = ($data['TotalVisibleMemorySize'] ?? 0) / 1024; // Convert KB to MB
                $free = ($data['FreePhysicalMemory'] ?? 0) / 1024;
                $used = $total - $free;
                $percentage = round(($used / $total) * 100, 2);
                
            } else {
                // Linux/Unix
                $free = shell_exec('free -m');
                $free = (string)trim($free);
                $free_arr = explode("\n", $free);
                $mem = explode(" ", $free_arr[1]);
                $mem = array_filter($mem);
                $mem = array_merge($mem);
                
                $total = $mem[1] ?? 0;
                $used = $mem[2] ?? 0;
                $free = $mem[3] ?? 0;
                $percentage = round(($used / $total) * 100, 2);
            }
            
            return [
                'total' => round($total, 2) . ' MB',
                'used' => round($used, 2) . ' MB',
                'free' => round($free, 2) . ' MB',
                'percentage' => $percentage,
                'human_total' => $this->formatBytes($total * 1024 * 1024),
                'human_used' => $this->formatBytes($used * 1024 * 1024),
                'human_free' => $this->formatBytes($free * 1024 * 1024),
            ];
            
        } catch (\Exception $e) {
            return [
                'total' => 'N/A',
                'used' => 'N/A',
                'free' => 'N/A',
                'percentage' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get Disk usage
     */
    private function getDiskUsage()
    {
        try {
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            $used = $total - $free;
            $percentage = round(($used / $total) * 100, 2);
            
            return [
                'total' => $this->formatBytes($total),
                'used' => $this->formatBytes($used),
                'free' => $this->formatBytes($free),
                'percentage' => $percentage,
            ];
            
        } catch (\Exception $e) {
            return [
                'total' => 'N/A',
                'used' => 'N/A',
                'free' => 'N/A',
                'percentage' => 0,
            ];
        }
    }
    
    /**
     * Get system load average
     */
    private function getSystemLoad()
    {
        try {
            if (function_exists('sys_getloadavg')) {
                $load = sys_getloadavg();
                return [
                    '1min' => round($load[0], 2),
                    '5min' => round($load[1], 2),
                    '15min' => round($load[2], 2),
                ];
            }
            
            return ['1min' => 0, '5min' => 0, '15min' => 0];
            
        } catch (\Exception $e) {
            return ['1min' => 0, '5min' => 0, '15min' => 0];
        }
    }
    
    /**
     * Get system uptime
     */
    private function getUptime()
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                $output = [];
                exec('net stats server', $output);
                $uptime = $output[3] ?? 'N/A';
            } else {
                // Linux/Unix
                $uptime = shell_exec('uptime -p');
                $uptime = str_replace('up ', '', trim($uptime));
            }
            
            return $uptime ?: 'N/A';
            
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    /**
     * Get CPU cores count
     */
    private function getCpuCores()
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $output = [];
                exec('wmic cpu get NumberOfCores', $output);
                return (int) trim($output[1] ?? 1);
            } else {
                return (int) shell_exec('nproc');
            }
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    /**
     * Get CPU model
     */
    private function getCpuModel()
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $output = [];
                exec('wmic cpu get name', $output);
                return trim($output[1] ?? 'Unknown');
            } else {
                $model = shell_exec('cat /proc/cpuinfo | grep "model name" | head -1');
                $model = str_replace('model name	:', '', $model);
                return trim($model ?: 'Unknown');
            }
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}