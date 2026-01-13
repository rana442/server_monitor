@extends('layouts.app')

@section('title', 'Add New Monitor')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="bi bi-plus-circle"></i> Add New Monitor
                </h1>
                <a href="{{ route('admin.monitors.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.monitors.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Monitor Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">URL *</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" 
                                   id="url" name="url" value="{{ old('url') }}" 
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
                                    <option value="">Select Type</option>
                                    <option value="http" {{ old('type') == 'http' ? 'selected' : '' }}>HTTP</option>
                                    <option value="ping" {{ old('type') == 'ping' ? 'selected' : '' }}>Ping</option>
                                    <option value="port" {{ old('type') == 'port' ? 'selected' : '' }}>Port</option>
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
                                    <option value="Core Device" {{ old('device_group') == 'Core Device' ? 'selected' : '' }}>Core Device</option>
                                    <option value="OLT" {{ old('device_group') == 'OLT' ? 'selected' : '' }}>OLT</option>
                                    <option value="Switch" {{ old('device_group') == 'Switch' ? 'selected' : '' }}>Switch</option>
                                    <option value="Camera" {{ old('device_group') == 'Camera' ? 'selected' : '' }}>Camera</option>
                                    <option value="Mikrotik" {{ old('device_group') == 'Mikrotik' ? 'selected' : '' }}>Mikrotik</option>
                                </select>
                                @error('device_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="interval" class="form-label">Check Interval (seconds) *</label>
                                <input type="number" class="form-control @error('interval') is-invalid @enderror" 
                                       id="interval" name="interval" value="{{ old('interval', 3) }}" 
                                       min="1" max="300" required>
                                @error('interval')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Check every N seconds (min: 1, max: 300)</small>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" 
                                   id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Active (start monitoring immediately)
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Monitor
                        </button>
                        <a href="{{ route('admin.monitors.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Information</h5>
                </div>
                <div class="card-body">
                    <h6>Monitor Types:</h6>
                    <ul class="mb-3">
                        <li><strong>HTTP:</strong> Check HTTP/HTTPS websites</li>
                        <li><strong>Ping:</strong> Ping servers (requires exec permission)</li>
                        <li><strong>Port:</strong> Check specific ports</li>
                    </ul>
                    
                    <h6>Recommended Settings:</h6>
                    <ul>
                        <li>Critical services: 3-5 seconds</li>
                        <li>Important services: 10-30 seconds</li>
                        <li>General monitoring: 60 seconds</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection