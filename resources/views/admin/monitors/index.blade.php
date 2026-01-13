@extends('layouts.app')

@section('title', 'Manage Monitors')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="bi bi-list-check"></i> Manage Monitors
                </h1>
                <a href="{{ route('admin.monitors.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Monitor
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search logs (status, response time, message...)"
               value="{{ request('search') }}">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Search
        </button>
    </div>

    @if(request('search'))
    <div class="col-md-2">
        <a href="{{ route('admin.monitors.index') }}"
           class="btn btn-secondary w-100">
            Clear
        </a>
    </div>
    @endif
</form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Device Group</th>
                            <th>URL</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Interval</th>
                            <th>Uptime</th>
                            <th>Last Check</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $deviceGroupColors = [
                                'Core Device' => 'primary',
                                'OLT'         => 'success',
                                'Switch'      => 'info',
                                'Camera'      => 'warning',
                                'Mikrotik'    => 'danger',
                            ];
                            @endphp
                        @forelse($monitors as $monitor)
                        <tr>
                            <td>{{ $monitor->id }}</td>
                            <td>{{ $monitor->name }}</td>
                            <td>
                                <span class="badge bg-{{ $deviceGroupColors[$monitor->device_group] ?? 'secondary' }}">
                                    {{ $monitor->device_group }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ $monitor->url }}" target="_blank" class="text-decoration-none">
                                    {{ Str::limit($monitor->url, 30) }}
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-info text-uppercase">
                                    {{ $monitor->type }}
                                </span>
                            </td>
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
                            <td>{{ $monitor->interval }}s</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 20px;">
                                        <div class="progress-bar {{ $monitor->uptime_percentage > 95 ? 'bg-success' : ($monitor->uptime_percentage > 80 ? 'bg-warning' : 'bg-danger') }}"
                                             role="progressbar"
                                             style="width: {{ $monitor->uptime_percentage }}%">
                                            {{ number_format($monitor->uptime_percentage, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($monitor->last_checked_at)
                                {{ $monitor->last_checked_at->diffForHumans() }}
                                @else
                                <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('monitors.show', $monitor) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.monitors.edit', $monitor) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.monitors.destroy', $monitor) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure?')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">No monitors found. Create your first monitor!</p>
                                    <a href="{{ route('admin.monitors.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle"></i> Add Monitor
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($monitors->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $monitors->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection