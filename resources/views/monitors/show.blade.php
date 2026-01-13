@extends('layouts.app')

@section('title', $monitor->name . ' - Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-server"></i> {{ $monitor->name }}
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $monitor->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('monitors.logs', $monitor) }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history"></i> View Logs
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitor Details Card -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Monitor Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Monitor Name:</th>
                                    <td>{{ $monitor->name }}</td>
                                </tr>
                                <tr>
                                    <th>URL:</th>
                                    <td>
                                        <a href="{{ $monitor->url }}" target="_blank" class="text-decoration-none">
                                            {{ $monitor->url }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge bg-info text-uppercase">
                                            {{ $monitor->type }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Check Interval:</th>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $monitor->interval }} seconds
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Current Status:</th>
                                    <td>
                                        @if($monitor->last_status === true)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> UP
                                        </span>
                                        @elseif($monitor->last_status === false)
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> DOWN
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-question-circle"></i> UNKNOWN
                                        </span>
                                        @endif
                                        
                                        @if($monitor->is_active)
                                        <span class="badge bg-primary ms-1">Active</span>
                                        @else
                                        <span class="badge bg-warning ms-1">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Uptime:</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 20px;">
                                                <div class="progress-bar {{ $monitor->uptime_percentage > 95 ? 'bg-success' : ($monitor->uptime_percentage > 80 ? 'bg-warning' : 'bg-danger') }}"
                                                     role="progressbar"
                                                     style="width: {{ $monitor->uptime_percentage }}%">
                                                    {{ number_format($monitor->uptime_percentage, 2) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Checked:</th>
                                    <td>
                                        @if($monitor->last_checked_at)
                                        {{ $monitor->last_checked_at->diffForHumans() }}
                                        @else
                                        <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $monitor->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status Duration Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> Current Status Duration</h5>
                </div>
                <div class="card-body text-center">
                    @if($monitor->last_status && $monitor->last_up_at)
                    <div class="display-4 text-success mb-2">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h3 class="text-success">UP</h3>
                    <p class="text-muted mb-0">Since</p>
                    <h5>{{ $monitor->last_up_at->format('Y-m-d H:i:s') }}</h5>
                    <p class="text-muted">{{ $monitor->last_up_at->diffForHumans() }}</p>
                    
                    @elseif(!$monitor->last_status && $monitor->last_down_at)
                    <div class="display-4 text-danger mb-2">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 class="text-danger">DOWN</h3>
                    <p class="text-muted mb-0">Since</p>
                    <h5>{{ $monitor->last_down_at->format('Y-m-d H:i:s') }}</h5>
                    <p class="text-muted">{{ $monitor->last_down_at->diffForHumans() }}</p>
                    
                    @else
                    <div class="display-4 text-secondary mb-2">
                        <i class="bi bi-question-circle-fill"></i>
                    </div>
                    <h3 class="text-secondary">UNKNOWN</h3>
                    <p class="text-muted">No status data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Response Time Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Response Time (Last 24 Hours)</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentLogs = $monitor->pingLogs()
                            ->where('created_at', '>=', now()->subDay())
                            ->orderBy('created_at')
                            ->get();
                        
                        $chartData = $recentLogs->map(function($log) {
                            return [
                                'x' => $log->created_at->format('H:i'),
                                'y' => $log->response_time ?? 0,
                                'status' => $log->status ? 'up' : 'down'
                            ];
                        });
                    @endphp
                    
                    @if($recentLogs->count() > 0)
                   
                    <canvas id="responseTimeChart" height="100"></canvas>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const data = @json($chartData);

                            const ctx = document.getElementById('responseTimeChart').getContext('2d');

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.map(d => d.x),
                                    datasets: [{
                                        label: 'Response Time (ms)',
                                        data: data.map(d => d.y),
                                        borderColor: '#0d6efd',
                                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                        fill: true,
                                        tension: 0.4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: { beginAtZero: true }
                                    }
                                }
                            });
                        });
                        </script>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-graph-up display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No response time data available for the last 24 hours.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-check"></i> Recent Activity</h5>
                    <a href="{{ route('monitors.logs', $monitor) }}" class="btn btn-sm btn-outline-primary">
                        View All Logs
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Response Time</th>
                                    <th>Status Code</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monitor->pingLogs()->latest()->take(10)->get() as $log)
                                <tr>
                                    <td>
                                        <span title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                            {{ $log->created_at->diffForHumans() }}
                                        </span>
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
                                        @if($log->status_code)
                                        <span class="badge {{ $log->status_code == 200 ? 'bg-success' : 'bg-warning' }}">
                                            {{ $log->status_code }}
                                        </span>
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->error_message)
                                        <span class="text-danger small" title="{{ $log->error_message }}">
                                            <i class="bi bi-exclamation-triangle"></i> {{ Str::limit($log->error_message, 50) }}
                                        </span>
                                        @elseif($log->response_body)
                                        <span class="text-muted small" title="{{ $log->response_body }}">
                                            {{ Str::limit($log->response_body, 50) }}
                                        </span>
                                        @else
                                        <span class="text-success small">
                                            <i class="bi bi-check-circle"></i> Successful
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="mt-2 text-muted">No activity logs found for this monitor.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charting Library -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<style>
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0.5rem;
    }
    
    .breadcrumb-item a {
        text-decoration: none;
    }
    
    table.table-borderless td, 
    table.table-borderless th {
        border: none;
        padding: 0.5rem 0;
    }
    
    table.table-borderless th {
        font-weight: 600;
        color: #6c757d;
    }
</style>
@endsection