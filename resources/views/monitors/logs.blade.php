@extends('layouts.app')

@section('title', $monitor->name . ' - Logs')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-clock-history"></i> {{ $monitor->name }} - Activity Logs
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('monitors.show', $monitor) }}">{{ $monitor->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Logs</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('monitors.show', $monitor) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Monitor
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Logs</h5>
                    <h2 class="mb-0">{{ $monitor->pingLogs()->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">UP Logs</h5>
                    <h2 class="mb-0">{{ $monitor->pingLogs()->where('status', true)->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">DOWN Logs</h5>
                    <h2 class="mb-0">{{ $monitor->pingLogs()->where('status', false)->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Avg Response</h5>
                    <h2 class="mb-0">
                        @php
                            $avgResponse = $monitor->pingLogs()
                                ->whereNotNull('response_time')
                                ->avg('response_time');
                            echo $avgResponse ? round($avgResponse) . 'ms' : 'N/A';
                        @endphp
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('monitors.logs', $monitor) }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="up" {{ request('status') == 'up' ? 'selected' : '' }}>UP Only</option>
                        <option value="down" {{ request('status') == 'down' ? 'selected' : '' }}>DOWN Only</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" 
                           class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" 
                           class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <a href="{{ route('monitors.logs', $monitor) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Timestamp</th>
                            <th>Status</th>
                            <th>Response Time</th>
                            <th>Status Code</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                <span title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($log->status)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> UP
                                </span>
                                @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle"></i> DOWN
                                </span>
                                @endif
                            </td>
                            <td>
                                @if($log->response_time)
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 10px;">
                                        @php
                                            $width = min($log->response_time / 10, 100);
                                            $color = $log->response_time < 100 ? 'bg-success' : 
                                                     ($log->response_time < 500 ? 'bg-warning' : 'bg-danger');
                                        @endphp
                                        <div class="progress-bar {{ $color }}" 
                                             style="width: {{ $width }}%"></div>
                                    </div>
                                    <span class="ms-2">{{ $log->response_time }}ms</span>
                                </div>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($log->status_code)
                                <span class="badge {{ $log->status_code == 200 ? 'bg-success' : 
                                                      ($log->status_code >= 400 && $log->status_code < 500 ? 'bg-warning' : 
                                                      ($log->status_code >= 500 ? 'bg-danger' : 'bg-info')) }}">
                                    {{ $log->status_code }}
                                </span>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($log->error_message)
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        data-bs-toggle="popover" 
                                        data-bs-title="Error Details"
                                        data-bs-content="{{ $log->error_message }}">
                                    <i class="bi bi-exclamation-triangle"></i> View Error
                                </button>
                                @elseif($log->response_body)
                                <button type="button" class="btn btn-sm btn-outline-warning"
                                        data-bs-toggle="popover"
                                        data-bs-title="Response Body"
                                        data-bs-content="{{ Str::limit($log->response_body, 200) }}">
                                    <i class="bi bi-file-text"></i> View Response
                                </button>
                                @else
                                <span class="text-success">
                                    <i class="bi bi-check-circle"></i> OK
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">No logs found for this monitor.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
        
        // Auto-refresh every 30 seconds if on logs page
        setInterval(() => {
            window.location.reload();
        }, 30000);
    });
</script>

<style>
    .progress {
        min-width: 80px;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0.5rem;
    }
    
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endsection