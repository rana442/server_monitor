<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'dark_mode',
        'notification_settings'
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'notification_settings' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}