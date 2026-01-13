<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Monitor;
use App\Models\PingLog;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        
        $totalUsers = User::count();
        $totalMonitors = Monitor::count();
        $activeMonitors = Monitor::where('is_active', true)->count();
        $totalPingLogs = PingLog::count();
        $uptimePercentage = Monitor::where('is_active', true)->avg('uptime_percentage') ?? 0;
        
        $recentUsers = User::latest()->take(5)->get();
        $recentMonitors = Monitor::latest()->take(5)->get();
        $recentLogs = PingLog::with('monitor')->latest()->take(10)->get();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMonitors',
            'activeMonitors',
            'totalPingLogs',
            'uptimePercentage',
            'recentUsers',
            'recentMonitors',
            'recentLogs'
        ));
    }
    
    /**
     * Display list of users.
     */
    public function users()
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }
    
    /**
     * Display system logs.
     */
    public function systemLogs()
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        
        $logs = PingLog::with('monitor')->latest()->paginate(50);
        return view('admin.logs.index', compact('logs'));
    }
    
    /**
     * Toggle user admin status.
     */
    public function toggleAdmin(User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        
        $user->is_admin = !$user->is_admin;
        $user->save();
        
        return back()->with('success', 'User admin status updated successfully.');
    }
    
    /**
     * Delete user.
     */
    public function deleteUser(User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return back()->with('success', 'User deleted successfully.');
    }
}