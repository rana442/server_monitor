<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $deviceGroupColors = $this->deviceGroupColors;
        $status = $request->get('status'); // up | down | null
        $deviceGroup  = $request->get('device_group');


        $query = Monitor::where('is_active', true);

        if ($status === 'up') {
            $query->where('last_status', true);
        } elseif ($status === 'down') {
            $query->where('last_status', false);
        }

        if ($deviceGroup) {
            $query->where('device_group', $deviceGroup);
        }

        $monitors = $query->get();

        // Stats always calculated from ALL active monitors
        $allMonitors = Monitor::where('is_active', true)->get();

        $totalMonitors = $allMonitors->count();
        $upMonitors = $allMonitors->where('last_status', true)->count();
        $downMonitors = $allMonitors->where('last_status', false)->count();
        $avgUptime = $allMonitors->avg('uptime_percentage') ?? 0;
        // return auth()->user();

        $deviceGroupCounts = Monitor::where('is_active', true)
            ->select('device_group', DB::raw('COUNT(*) as total'))
            ->groupBy('device_group')
            ->pluck('total', 'device_group'); 
        
        return view('dashboard.index', compact(
            'monitors',
            'totalMonitors',
            'upMonitors',
            'downMonitors',
            'avgUptime',
            'status',
            'deviceGroup',
            'deviceGroupCounts',
            'deviceGroupColors',
        ));
    }

    // public function partial(Request $request)
    // {
    //     $status = $request->get('status');

    //     $query = Monitor::where('is_active', true);

    //     if ($status === 'up') {
    //         $query->where('last_status', true);
    //     } elseif ($status === 'down') {
    //         $query->where('last_status', false);
    //     }

    //     $monitors = $query->get();

    //     $allMonitors = Monitor::where('is_active', true)->get();

    //     return view('dashboard.partials.data', [
    //         'monitors'       => $monitors,
    //         'totalMonitors'  => $allMonitors->count(),
    //         'upMonitors'     => $allMonitors->where('last_status', true)->count(),
    //         'downMonitors'   => $allMonitors->where('last_status', false)->count(),
    //         'avgUptime'      => $allMonitors->avg('uptime_percentage') ?? 0,
    //     ]);
    // }

    public function partial(Request $request)
    {
        $deviceGroupColors = $this->deviceGroupColors;
        $status       = $request->get('status');
        $deviceGroup  = $request->get('device_group');

        $query = Monitor::where('is_active', true);

        // Status filter
        if ($status === 'up') {
            $query->where('last_status', true);
        } elseif ($status === 'down') {
            $query->where('last_status', false);
        }

        // Device group filter
        if ($deviceGroup) {
            $query->where('device_group', $deviceGroup);
        }

        $monitors = $query->get();

        $allMonitors = Monitor::where('is_active', true)->get();

        // Device group counts (LIVE)
        $deviceGroupCounts = Monitor::where('is_active', true)
            ->select('device_group', DB::raw('COUNT(*) as total'))
            ->groupBy('device_group')
            ->pluck('total', 'device_group');

        // return view('dashboard.partials.data', [
        //     'monitors'            => $monitors,
        //     'totalMonitors'       => $allMonitors->count(),
        //     'upMonitors'          => $allMonitors->where('last_status', true)->count(),
        //     'downMonitors'        => $allMonitors->where('last_status', false)->count(),
        //     'avgUptime'           => $allMonitors->avg('uptime_percentage') ?? 0,
        //     'deviceGroupCounts'   => $deviceGroupCounts,
        // ]);
        /* ===============================
        ✅ JSON MODE (for live update)
        =============================== */
        if ($request->expectsJson()) {
            return response()->json([
                'stats' => [
                    'total' => $allMonitors->count(),
                    'up'    => $allMonitors->where('last_status', true)->count(),
                    'down'  => $allMonitors->where('last_status', false)->count(),
                    'avg'   => number_format($allMonitors->avg('uptime_percentage') ?? 0, 2),
                ],
                'deviceGroups' => $deviceGroupCounts,
            ]);
        }

        /* ===============================
        ✅ HTML MODE (table rows only)
        =============================== */
        return view('dashboard.partials.data', [
            'monitors' => $monitors,
            'deviceGroupColors' => $deviceGroupColors,
        ]);
    }



    private function getSystemStats()
    {
        // Use the SystemStatsController or calculate here
        try {
            $statsController = new SystemStatsController();
            return $statsController->getStats();
        } catch (\Exception $e) {
            return [
                'cpu' => ['percentage' => 0, 'cores' => 'N/A'],
                'memory' => ['percentage' => 0, 'total' => 'N/A', 'used' => 'N/A'],
                'disk' => ['percentage' => 0, 'total' => 'N/A', 'used' => 'N/A'],
                'load' => ['1min' => 0, '5min' => 0, '15min' => 0],
                'uptime' => 'N/A',
                'timestamp' => now()->toDateTimeString(),
            ];
        }
    }
}