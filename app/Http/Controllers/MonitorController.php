<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        
        
    }
    public function index(Request $request)
    {
        $search = $request->get('search');

        $monitors = Monitor::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('device_group', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // keep search on pagination

        return view('admin.monitors.index', compact('monitors'));
    }
    // public function index()
    // {
    //     $monitors = Monitor::latest()->paginate(10);
    //     return view('admin.monitors.index', compact('monitors'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deviceGroups =  $this->deviceGroups;
        return view('admin.monitors.create',compact('deviceGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required',
            'type' => 'required|in:http,ping,port',
            'interval' => 'required|integer|min:1|max:300',
            'device_group' => 'required|in:Core Device,OLT,Switch,Camera,Mikrotik',
        ]);

        Monitor::create([
            'name' => $request->name,
            'url' => $request->url,
            'type' => $request->type,
            'interval' => $request->interval,
            'device_group' => $request->device_group,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.monitors.index')
            ->with('success', 'Monitor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Monitor $monitor)
    {
        $logs = $monitor->pingLogs()->latest()->paginate(20);
        return view('monitors.show', compact('monitor', 'logs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Monitor $monitor)
    {
        // return $monitor;
        $deviceGroups =  $this->deviceGroups;

        return view('admin.monitors.edit', compact('monitor','deviceGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Monitor $monitor)
    {
        // return $request->all();
        // Check if user is admin (for admin routes)
        if (request()->is('admin/*') && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required',
            'type' => 'required|in:http,ping,port',
            'interval' => 'required|integer|min:1|max:300',
            'timeout' => 'nullable|integer|min:1|max:60',
            'retries' => 'nullable|integer|min:0|max:5',
            'notify_on_down' => 'nullable|boolean',
            'notify_on_up' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'device_group' => 'required|in:Core Device,OLT,Switch,Camera,Mikrotik',
        ]);
        
        $monitor->update([
            'name' => $request->name,
            'url' => $request->url,
            'type' => $request->type,
            'interval' => $request->interval,
            'timeout' => $request->timeout ?? 10,
            'retries' => $request->retries ?? 1,
            'notify_on_down' => $request->boolean('notify_on_down'),
            'notify_on_up' => $request->boolean('notify_on_up'),
            'is_active' => $request->boolean('is_active'),
            'device_group' => $request->device_group,
        ]);
        
        $route = request()->is('admin/*') ? 'admin.monitors.index' : 'monitors.show';
        
        return redirect()->route($route, $monitor)
            ->with('success', 'Monitor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Monitor $monitor)
    {
        $monitor->delete();
        
        return redirect()->route('admin.monitors.index')
            ->with('success', 'Monitor deleted successfully.');
    }

    /**
     * Show monitor logs
     */
    public function logs(Monitor $monitor)
    {
        $query = $monitor->pingLogs()->latest();
        
        // Apply filters
        if (request('status') == 'up') {
            $query->where('status', true);
        } elseif (request('status') == 'down') {
            $query->where('status', false);
        }
        
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }
        
        $logs = $query->paginate(50);
        
        return view('monitors.logs', compact('monitor', 'logs'));
    }
}