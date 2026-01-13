@php
$deviceGroupColors = [
    'Core Device' => 'primary',
    'OLT'         => 'success',
    'Switch'      => 'info',
    'Camera'      => 'warning',
    'Mikrotik'    => 'danger',
];
@endphp


{{-- TABLE --}}
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
            <div class="progress-bar
                {{ $monitor->uptime_percentage > 95 ? 'bg-success' :
                   ($monitor->uptime_percentage > 80 ? 'bg-warning' : 'bg-danger') }}"
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
        <a href="{{ route('monitors.show', $monitor) }}"
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-eye"></i> View
        </a>
    </td>
</tr>
@endforeach

