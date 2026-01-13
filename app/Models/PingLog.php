<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PingLog extends Model
{
    protected $fillable = [
        'monitor_id',
        'status',
        'response_time',
        'status_code',
        'response_body',
        'error_message'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}