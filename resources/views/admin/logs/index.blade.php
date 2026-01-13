@extends('layouts.app')

@section('title', 'System Logs')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="bi bi-clock-history"></i> System Logs
                </h1>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <button onclick="location.reload()" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Timestamp</th>
                            <th>Monitor</th>
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
                                <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small><br>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($log->monitor)
                                <a href="{{ route('monitors.show', $log->monitor) }}" class="text-decoration-none">
                                    {{ $log->monitor->name }}
                                </a>
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
                                    <i class="bi bi-exclamation-triangle"></i> Error
                                </button>
                                @elseif($log->response_body)
                                <button type="button" class="btn btn-sm btn-outline-warning"
                                        data-bs-toggle="popover"
                                        data-bs-title="Response Body"
                                        data-bs-content="{{ Str::limit($log->response_body, 200) }}">
                                    <i class="bi bi-file-text"></i> Response
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
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">No system logs found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links() }}
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
    });
</script>
@endsection