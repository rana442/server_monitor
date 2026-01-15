@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">
            <i class="bi bi-speedometer2"></i> Server Status Dashboard
        </h1>
        <p class="text-muted">
                Last updated: <span id="current-time">{{ now()->format('Y-m-d H:i:s') }}</span>
                • Auto refresh: <span class="badge text-bg-warning" id="refresh-status">Enabled</span>
            </p>
    </div>
</div>

<div class="row mb-4">
    <!-- Stats Cards -->
    <div class="col-md-3 mb-3">
        <a href="{{ route('dashboard') }}" class="text-decoration-none">
            <div class="card bg-primary text-white {{ request('status') ? 'opacity-75' : '' }}">
                <div class="card-body">
                    <h5 class="card-title">Total Monitors</h5>
                    <h2 class="mb-0" id="stat-total">{{ $totalMonitors }}</h2>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('dashboard', ['status' => 'up']) }}" class="text-decoration-none">
            <div class="card bg-success text-white {{ request('status') === 'up' ? 'border border-3 border-light' : '' }}">
                <div class="card-body">
                    <h5 class="card-title">Up</h5>
                    <h2 class="mb-0" id="stat-up">{{ $upMonitors }}</h2>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('dashboard', ['status' => 'down']) }}" class="text-decoration-none">
            <div class="card bg-danger text-white {{ request('status') === 'down' ? 'border border-3 border-light' : '' }}">
                <div class="card-body">
                    <h5 class="card-title">Down</h5>
                    <h2 class="mb-0" id="stat-down">{{ $downMonitors }}</h2>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Avg. Uptime</h5>
                <h2 class="mb-0" id="stat-avg">{{ number_format($avgUptime, 2) }}%</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3" id="device-group-cards">
@foreach($deviceGroupColors as $group => $color)
<div class="col-md-2 col-6 mb-2">
    <div class="card text-center border-{{ $color }}">
        <div class="card-body py-2">
            <div class="fw-semibold text-{{ $color }}">
                {{ $group }}
            </div>
            <div class="fs-4"
                 data-device-group="{{ $group }}">
                {{ $deviceGroupCounts[$group] ?? 0 }}
            </div>
        </div>
    </div>
</div>
@endforeach
</div>



@if($status)
<div class="alert alert-info py-2">
    Showing:
    <strong>{{ strtoupper($status) }}</strong> monitors
    <a href="{{ route('dashboard') }}" class="ms-2">(Clear filter)</a>
</div>
@endif
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list-check"></i> All Monitors
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Device Group</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Last Check</th>
                        <th>Uptime</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="monitor-table">
                    @foreach($monitors as $monitor)
                    <tr>
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
                            @if($monitor->last_status)
                            <span class="badge status-badge status-up">
                                <i class="bi bi-check-circle"></i> UP
                            </span>
                            @else
                            <span class="badge status-badge status-down">
                                <i class="bi bi-x-circle"></i> DOWN
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($monitor->last_checked_at)
                            {{ $monitor->last_checked_at->diffForHumans() }}
                            @else
                            <span class="text-muted">Never</span>
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
                            @if($monitor->last_status && $monitor->last_up_at)
                            <span class="badge bg-success duration-badge">
                                {{ $monitor->last_up_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                            </span>
                            @elseif(!$monitor->last_status && $monitor->last_down_at)
                            <span class="badge bg-danger duration-badge">
                                {{ $monitor->last_down_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                            </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('monitors.show', $monitor) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- JavaScript for auto-update -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let autoRefresh = true;
    let timer = null;
    const REFRESH_TIME = 10000;

    const badge = document.getElementById('refresh-status');
    const table = document.getElementById('monitor-table');
    const timeEl = document.getElementById('current-time');

    /* ======================
       LIVE CLOCK (every sec)
    ====================== */
    updateClock();
    setInterval(updateClock, 1000);

    function updateClock() {
        const now = new Date();
        const pad = n => n.toString().padStart(2, '0');

        timeEl.textContent =
            now.getFullYear() + '-' +
            pad(now.getMonth() + 1) + '-' +
            pad(now.getDate()) + ' ' +
            pad(now.getHours()) + ':' +
            pad(now.getMinutes()) + ':' +
            pad(now.getSeconds());
    }
    
    start();
    updateBadge();

    badge.onclick = () => {
        autoRefresh = !autoRefresh;
        autoRefresh ? start() : stop();
        updateBadge();
    };

    function start() {
        stop();
        timer = setInterval(loadData, REFRESH_TIME);
    }

    function stop() {
        if (timer) clearInterval(timer);
    }

    function updateBadge() {
        badge.textContent = autoRefresh ? 'Enabled' : 'Disabled';
        badge.className = autoRefresh
            ? 'badge text-bg-warning'
            : 'badge text-bg-secondary';
        badge.style.cursor = 'pointer';
    }

    // async function loadData() {
    //     if (!autoRefresh) return;

    //     const url = "{{ route('dashboard.partial') }}?{{ request()->getQueryString() }}";
    //     const res = await fetch(url);
    //     const html = await res.text();

    //     table.innerHTML = html;
    // }
    async function loadData() {
        if (!autoRefresh) return;

        const baseUrl = "{{ route('dashboard.partial') }}?{{ request()->getQueryString() }}";

        /* TABLE (HTML) */
        const htmlRes = await fetch(baseUrl);
        table.innerHTML = await htmlRes.text();

        /* STATS + COUNTS (JSON) */
        const jsonRes = await fetch(baseUrl, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await jsonRes.json();

        // Stats
        document.getElementById('stat-total').textContent = data.stats.total;
        document.getElementById('stat-up').textContent    = data.stats.up;
        document.getElementById('stat-down').textContent  = data.stats.down;
        document.getElementById('stat-avg').textContent   = data.stats.avg + '%';

        // Device group counts
        document.querySelectorAll('[data-device-group]').forEach(el => {
            const group = el.dataset.deviceGroup;
            el.textContent = data.deviceGroups[group] ?? 0;
        });
    }

});
</script>
@endpush

@endsection