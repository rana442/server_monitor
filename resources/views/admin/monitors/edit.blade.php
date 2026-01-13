@extends('layouts.app')

@section('title', 'Edit Monitor - ' . $monitor->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="bi bi-pencil-square"></i> Edit Monitor
                </h1>
                <div>
                    <a href="{{ route('admin.monitors.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('monitors.show', $monitor) }}" class="btn btn-info ms-2">
                        <i class="bi bi-eye"></i> View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monitor Settings</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.monitors.update', $monitor) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Monitor Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $monitor->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">URL *</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" 
                                   id="url" name="url" value="{{ old('url', $monitor->url) }}" 
                                   placeholder="https://example.com" required>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="http" {{ old('type', $monitor->type) == 'http' ? 'selected' : '' }}>HTTP/HTTPS</option>
                                    <option value="ping" {{ old('type', $monitor->type) == 'ping' ? 'selected' : '' }}>Ping (ICMP)</option>
                                    <option value="port" {{ old('type', $monitor->type) == 'port' ? 'selected' : '' }}>Port Check</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="device_group" class="form-label">Group *</label>
                                <select class="form-select @error('device_group') is-invalid @enderror" 
                                        id="device_group" name="device_group" required>
                                    <option value="">Select Group</option>
                                    <option value="Core Device" {{ old('device_group', $monitor->device_group) == 'Core Device' ? 'selected' : '' }}>Core Device</option>
                                    <option value="OLT" {{ old('device_group', $monitor->device_group) == 'OLT' ? 'selected' : '' }}>OLT</option>
                                    <option value="Switch" {{ old('device_group', $monitor->device_group) == 'Switch' ? 'selected' : '' }}>Switch</option>
                                    <option value="Camera" {{ old('device_group', $monitor->device_group) == 'Camera' ? 'selected' : '' }}>Camera</option>
                                    <option value="Mikrotik" {{ old('device_group', $monitor->device_group) == 'Mikrotik' ? 'selected' : '' }}>Mikrotik</option>
                                </select>
                                @error('device_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="interval" class="form-label">Check Interval (seconds) *</label>
                                <input type="number" class="form-control @error('interval') is-invalid @enderror" 
                                       id="interval" name="interval" value="{{ old('interval', $monitor->interval) }}" 
                                       min="1" max="300" required>
                                @error('interval')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How often to check this monitor (1-300 seconds)</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="timeout" class="form-label">Timeout (seconds)</label>
                                <input type="number" class="form-control @error('timeout') is-invalid @enderror" 
                                       id="timeout" name="timeout" value="{{ old('timeout', $monitor->timeout ?? 10) }}" 
                                       min="1" max="60">
                                @error('timeout')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Timeout for each check (default: 10s)</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="retries" class="form-label">Retry Count</label>
                                <input type="number" class="form-control @error('retries') is-invalid @enderror" 
                                       id="retries" name="retries" value="{{ old('retries', $monitor->retries ?? 1) }}" 
                                       min="0" max="5">
                                @error('retries')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of retries before marking as DOWN (default: 1)</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notification Settings</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="notify_on_down" name="notify_on_down" value="1"
                                       {{ old('notify_on_down', $monitor->notify_on_down ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_on_down">
                                    Send notification when monitor goes DOWN
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="notify_on_up" name="notify_on_up" value="1"
                                       {{ old('notify_on_up', $monitor->notify_on_up ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_on_up">
                                    Send notification when monitor comes back UP
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $monitor->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Monitor is Active</strong>
                                </label>
                                <div class="form-text">
                                    When disabled, this monitor will not be checked automatically.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Monitor
                            </button>
                            
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Delete Monitor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Monitor Stats Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Current Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Current Status:</strong><br>
                        @if($monitor->last_status === true)
                        <span class="badge bg-success fs-6 mt-1">
                            <i class="bi bi-check-circle"></i> UP
                        </span>
                        @elseif($monitor->last_status === false)
                        <span class="badge bg-danger fs-6 mt-1">
                            <i class="bi bi-x-circle"></i> DOWN
                        </span>
                        @else
                        <span class="badge bg-secondary fs-6 mt-1">
                            <i class="bi bi-question-circle"></i> UNKNOWN
                        </span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>Uptime:</strong>
                        <div class="progress mt-1" style="height: 25px;">
                            <div class="progress-bar {{ $monitor->uptime_percentage > 95 ? 'bg-success' : ($monitor->uptime_percentage > 80 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar"
                                 style="width: {{ $monitor->uptime_percentage }}%"
                                 aria-valuenow="{{ $monitor->uptime_percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ number_format($monitor->uptime_percentage, 2) }}%
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Last Checked:</strong><br>
                        @if($monitor->last_checked_at)
                        <span class="text-muted">{{ $monitor->last_checked_at->diffForHumans() }}</span><br>
                        <small>{{ $monitor->last_checked_at->format('Y-m-d H:i:s') }}</small>
                        @else
                        <span class="text-muted">Never</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>Total Checks:</strong><br>
                        <span class="badge bg-info">{{ $monitor->pingLogs()->count() }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $monitor->created_at->format('Y-m-d H:i:s') }}<br>
                        <small class="text-muted">{{ $monitor->created_at->diffForHumans() }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        {{ $monitor->updated_at->format('Y-m-d H:i:s') }}<br>
                        <small class="text-muted">{{ $monitor->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('monitors.logs', $monitor) }}" class="btn btn-outline-primary">
                            <i class="bi bi-clock-history"></i> View Activity Logs
                        </a>
                        <button type="button" class="btn btn-outline-success" id="testMonitorBtn">
                            <i class="bi bi-play-circle"></i> Test Monitor Now
                        </button>
                        <button type="button" class="btn btn-outline-warning" id="resetStatsBtn">
                            <i class="bi bi-arrow-clockwise"></i> Reset Statistics
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Delete Monitor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger">
                    <strong>Warning:</strong> This action cannot be undone!
                </p>
                <p>You are about to delete the monitor <strong>"{{ $monitor->name }}"</strong>.</p>
                <p>This will also delete all associated ping logs (<strong>{{ $monitor->pingLogs()->count() }}</strong> records).</p>
                
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i> Are you sure you want to proceed?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.monitors.destroy', $monitor) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Monitor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Test Monitor Button
        document.getElementById('testMonitorBtn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Testing...';
            btn.disabled = true;
            
            fetch('{{ route("admin.monitors.test", $monitor) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Monitor test successful!\nStatus: ' + data.status + '\nResponse Time: ' + data.response_time + 'ms');
                } else {
                    alert('Monitor test failed!\nError: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
        
        // Reset Statistics Button
        document.getElementById('resetStatsBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to reset statistics for this monitor?\nThis will reset uptime percentage to 100% and clear status history.')) {
                fetch('{{ route("admin.monitors.reset", $monitor) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Statistics reset successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        });
        
       
       
    });
</script>

<style>
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .badge.fs-6 {
        font-size: 1rem;
        padding: 0.5em 1em;
    }
</style>
@endsection