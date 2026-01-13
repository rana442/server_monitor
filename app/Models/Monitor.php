<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monitor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'url',
        'type',
        'device_group',
        'interval',
        'is_active',
        'last_checked_at',
        'last_status',
        'last_up_at',
        'last_down_at',
        'uptime_percentage',
        'timeout', // নতুন যোগ করুন
        'retries', // নতুন যোগ করুন
        'notify_on_down', // নতুন যোগ করুন
        'notify_on_up', // নতুন যোগ করুন
    ];

    protected $casts = [
        'last_status' => 'boolean',
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
        'last_up_at' => 'datetime',
        'last_down_at' => 'datetime',
        'timeout' => 'integer',
        'retries' => 'integer',
        'notify_on_down' => 'boolean',
        'notify_on_up' => 'boolean',
    ];

    public function pingLogs()
    {
        return $this->hasMany(PingLog::class)->latest();
    }

    public function calculateUptime()
    {
        $totalLogs = $this->pingLogs()->count();
        $upLogs = $this->pingLogs()->where('status', true)->count();
        
        if ($totalLogs > 0) {
            $this->uptime_percentage = ($upLogs / $totalLogs) * 100;
            $this->save();
        }
    }
}