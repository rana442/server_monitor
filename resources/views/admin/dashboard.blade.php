@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="bi bi-shield-check"></i> Admin Dashboard
            </h1>
            <p class="text-muted">Welcome, {{ auth()->user()->name }}</p>
             • Auto refresh: <span class="badge text-bg-warning" id="refresh-status">Enabled</span>
        </div>
    </div>
<!-- System Stats Row -->
<div class="row mb-4">
    <!-- CPU Usage -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cpu"></i> CPU Usage
                    </h5>
                    <span class="badge bg-dark" id="cpu-cores">
                        {{ $systemStats['cpu']['cores'] ?? 'N/A' }} Cores
                    </span>
                </div>
                
                <div class="text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <div class="gauge" id="cpu-gauge" 
                                data-percentage="{{ $systemStats['cpu']['percentage'] ?? 0 }}">
                            <div class="gauge-body">
                                <div class="gauge-fill"></div>
                                <div class="gauge-cover">
                                    <span class="gauge-value" id="cpu-percentage">
                                        {{ $systemStats['cpu']['percentage'] ?? 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center small text-muted">
                    <div id="cpu-model">{{ $systemStats['cpu']['model'] ?? 'N/A' }}</div>
                    <div class="mt-1">
                        Load: 
                        <span id="load-1min">{{ $systemStats['load']['1min'] ?? 0 }}</span> | 
                        <span id="load-5min">{{ $systemStats['load']['5min'] ?? 0 }}</span> | 
                        <span id="load-15min">{{ $systemStats['load']['15min'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memory Usage -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-memory"></i> Memory Usage
                </h5>
                
                <div class="text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <div class="gauge" id="memory-gauge" 
                                data-percentage="{{ $systemStats['memory']['percentage'] ?? 0 }}">
                            <div class="gauge-body">
                                <div class="gauge-fill"></div>
                                <div class="gauge-cover">
                                    <span class="gauge-value" id="memory-percentage">
                                        {{ $systemStats['memory']['percentage'] ?? 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="row small">
                        <div class="col-6">
                            <div class="text-success">
                                <i class="bi bi-check-circle"></i> Free
                            </div>
                            <div class="fw-bold" id="memory-free">
                                {{ $systemStats['memory']['human_free'] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-warning">
                                <i class="bi bi-exclamation-circle"></i> Used
                            </div>
                            <div class="fw-bold" id="memory-used">
                                {{ $systemStats['memory']['human_used'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small" id="memory-total">
                        Total: {{ $systemStats['memory']['human_total'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disk Usage -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-hdd"></i> Disk Usage
                </h5>
                
                <div class="text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <div class="gauge" id="disk-gauge" 
                                data-percentage="{{ $systemStats['disk']['percentage'] ?? 0 }}">
                            <div class="gauge-body">
                                <div class="gauge-fill"></div>
                                <div class="gauge-cover">
                                    <span class="gauge-value" id="disk-percentage">
                                        {{ $systemStats['disk']['percentage'] ?? 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="row small">
                        <div class="col-6">
                            <div class="text-success">
                                <i class="bi bi-check-circle"></i> Free
                            </div>
                            <div class="fw-bold" id="disk-free">
                                {{ $systemStats['disk']['free'] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-warning">
                                <i class="bi bi-exclamation-circle"></i> Used
                            </div>
                            <div class="fw-bold" id="disk-used">
                                {{ $systemStats['disk']['used'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small" id="disk-total">
                        Total: {{ $systemStats['disk']['total'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="col-md-3 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-info-circle"></i> System Information
                </h5>
                
                <div class="system-info">
                    <div class="mb-2">
                        <div class="text-muted small">Server Uptime</div>
                        <div class="fw-bold" id="system-uptime">
                            {{ $systemStats['uptime'] ?? 'N/A' }}
                        </div>
                    </div>
                    
                    <!-- <div class="mb-3">
                        <div class="text-muted small">PHP Version</div>
                        <div class="fw-bold">{{ PHP_VERSION }}</div>
                    </div> -->
                    
                    <!-- <div class="mb-3">
                        <div class="text-muted small">Laravel Version</div>
                        <div class="fw-bold">{{ app()->version() }}</div>
                    </div> -->
                    
                    <!-- <div class="mb-3">
                        <div class="text-muted small">Server Software</div>
                        <div class="fw-bold">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' }}</div>
                    </div> -->
                    
                    <div class="mb-2">
                        <div class="text-muted small">Last Update</div>
                        <div class="fw-bold" id="stats-timestamp">
                            {{ $systemStats['timestamp'] ?? now()->format('Y-m-d H:i:s') }}
                        </div>
                    </div>
                </div>
                
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-primary w-100" id="refresh-stats">
                        <i class="bi bi-arrow-clockwise"></i> Refresh Stats
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monitor Stats Row -->
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2 class="mb-0">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Monitors</h5>
                    <h2 class="mb-0">{{ $totalMonitors }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Active Monitors</h5>
                    <h2 class="mb-0">{{ $activeMonitors }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Avg. Uptime</h5>
                    <h2 class="mb-0">{{ number_format($uptimePercentage, 2) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Recent Users</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_admin)
                                        <span class="badge bg-danger">Admin</span>
                                        @else
                                        <span class="badge bg-secondary">User</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-warning btn-sm" 
                                                        title="{{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}">
                                                    <i class="bi bi-shield{{ $user->is_admin ? '-minus' : '-plus' }}"></i>
                                                </button>
                                            </form>
                                            
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to delete this user?')"
                                                        title="Delete User">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Monitors -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-check"></i> Recent Monitors</h5>
                    <a href="{{ route('admin.monitors.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Uptime</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMonitors as $monitor)
                                <tr>
                                    <td>{{ $monitor->name }}</td>
                                    <td class="text-truncate" style="max-width: 150px;">
                                        {{ $monitor->url }}
                                    </td>
                                    <td>
                                        @if($monitor->last_status)
                                        <span class="badge bg-success">UP</span>
                                        @else
                                        <span class="badge bg-danger">DOWN</span>
                                        @endif
                                        
                                        @if($monitor->is_active)
                                        <span class="badge bg-primary ms-1">Active</span>
                                        @else
                                        <span class="badge bg-warning ms-1">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $monitor->uptime_percentage > 95 ? 'bg-success' : ($monitor->uptime_percentage > 80 ? 'bg-warning' : 'bg-danger') }}"
                                                 role="progressbar"
                                                 style="width: {{ $monitor->uptime_percentage }}%">
                                                {{ number_format($monitor->uptime_percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('monitors.show', $monitor) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Activity Logs</h5>
                    <a href="{{ route('admin.system.logs') }}" class="btn btn-sm btn-outline-primary">
                        View All Logs
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Monitor</th>
                                    <th>Status</th>
                                    <th>Response Time</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td>
                                        <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small><br>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($log->monitor)
                                        {{ $log->monitor->name }}
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->status)
                                        <span class="badge bg-success">UP</span>
                                        @else
                                        <span class="badge bg-danger">DOWN</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->response_time)
                                        <span class="badge bg-info">{{ $log->response_time }}ms</span>
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->error_message)
                                        <span class="text-danger small" title="{{ $log->error_message }}">
                                            <i class="bi bi-exclamation-triangle"></i> {{ Str::limit($log->error_message, 50) }}
                                        </span>
                                        @else
                                        <span class="text-success small">
                                            <i class="bi bi-check-circle"></i> OK
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gauge CSS -->
<style>
    .gauge {
        width: 120px;
        height: 120px;
    }
    
    .gauge-body {
        width: 100%;
        height: 100%;
        background: #f0f0f0;
        border-radius: 50%;
        position: relative;
        overflow: hidden;
    }
    
    .gauge-fill {
        position: absolute;
        background: linear-gradient(to right, #28a745, #ffc107, #dc3545);
        width: 100%;
        height: 100%;
        border-radius: 50%;
        clip-path: polygon(50% 50%, 50% 0%, 100% 0%, 100% 100%, 0% 100%, 0% 0%, 50% 0%);
        transform: rotate(0deg);
        transition: transform 0.5s ease;
    }
    
    .gauge-cover {
        width: 70%;
        height: 70%;
        background: white;
        border-radius: 50%;
        position: absolute;
        top: 15%;
        left: 15%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .gauge-value {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    .status-up {
        background-color: #198754;
        color: white;
    }
    
    .status-down {
        background-color: #dc3545;
        color: white;
    }
    
    .system-info div {
        padding: 0.25rem 0;
    }
</style>

<!-- JavaScript for auto-update -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let autoRefresh = true;
        let refreshInterval = null;
        
        // Initialize gauges
        initializeGauges();
        
        // Start auto-refresh
        startAutoRefresh();
        
        // Update current time every second
        // setInterval(updateCurrentTime, 1000);
        
        // Refresh stats button
        document.getElementById('refresh-stats').addEventListener('click', function() {
            fetchSystemStats();
        });
        
        // Toggle auto-refresh
        document.getElementById('refresh-status').addEventListener('click', function() {
            autoRefresh = !autoRefresh;
            this.textContent = autoRefresh ? 'Enabled' : 'Disabled';
            
            if (autoRefresh) {
                startAutoRefresh();
            } else {
                stopAutoRefresh();
            }
        });
        
        function initializeGauges() {
            const gauges = document.querySelectorAll('.gauge');
            gauges.forEach(gauge => {
                const percentage = parseFloat(gauge.dataset.percentage);
                const fill = gauge.querySelector('.gauge-fill');
                const rotation = (percentage / 100) * 180;
                fill.style.transform = `rotate(${rotation}deg)`;
                
                // Set color based on percentage
                if (percentage <= 50) {
                    fill.style.background = 'linear-gradient(to right, #28a745, #ffc107)';
                } else if (percentage <= 80) {
                    fill.style.background = 'linear-gradient(to right, #ffc107, #fd7e14)';
                } else {
                    fill.style.background = 'linear-gradient(to right, #fd7e14, #dc3545)';
                }
            });
        }
        
        function startAutoRefresh() {
            if (refreshInterval) clearInterval(refreshInterval);
            refreshInterval = setInterval(fetchSystemStats, 10000); // 10 seconds
        }
        
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        }
        
        function updateCurrentTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = 
                now.toISOString().replace('T', ' ').substr(0, 19);
        }
        
        function fetchSystemStats() {
            fetch('{{ route("admin.system.stats") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                updateSystemStats(data);
            })
            .catch(error => {
                console.error('Error fetching system stats:', error);
            });
        }
        
        function updateSystemStats(stats) {
            // Update CPU
            document.getElementById('cpu-percentage').textContent = stats.cpu.percentage + '%';
            document.getElementById('cpu-cores').textContent = stats.cpu.cores + ' Cores';
            document.getElementById('cpu-model').textContent = stats.cpu.model;
            
            // Update Memory
            document.getElementById('memory-percentage').textContent = stats.memory.percentage + '%';
            document.getElementById('memory-total').textContent = 'Total: ' + stats.memory.human_total;
            document.getElementById('memory-used').textContent = stats.memory.human_used;
            document.getElementById('memory-free').textContent = stats.memory.human_free;
            
            // Update Disk
            document.getElementById('disk-percentage').textContent = stats.disk.percentage + '%';
            document.getElementById('disk-total').textContent = 'Total: ' + stats.disk.total;
            document.getElementById('disk-used').textContent = stats.disk.used;
            document.getElementById('disk-free').textContent = stats.disk.free;
            
            // Update Load
            document.getElementById('load-1min').textContent = stats.load['1min'];
            document.getElementById('load-5min').textContent = stats.load['5min'];
            document.getElementById('load-15min').textContent = stats.load['15min'];
            
            // Update Uptime and Timestamp
            document.getElementById('system-uptime').textContent = stats.uptime;
            document.getElementById('stats-timestamp').textContent = stats.timestamp;
            
            // Update gauges
            document.getElementById('cpu-gauge').dataset.percentage = stats.cpu.percentage;
            document.getElementById('memory-gauge').dataset.percentage = stats.memory.percentage;
            document.getElementById('disk-gauge').dataset.percentage = stats.disk.percentage;
            
            initializeGauges();
        }
        
        // Initial fetch
        fetchSystemStats();
        
        // // Auto-refresh page every 30 seconds for monitor updates
        // setInterval(() => {
        //     if (autoRefresh) {
        //         window.location.reload();
        //     }
        // }, 30000);

        // Auto refresh every 5 seconds for dashboard
        @if(Route::currentRouteName() == 'dashboard')
        setInterval(() => {
            if (autoRefresh) {
                 window.location.reload();
             }
        }, 10000);
        @endif
        
    });
</script>
@endpush
@endsection