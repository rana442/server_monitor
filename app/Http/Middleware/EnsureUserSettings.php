<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserSetting;

class EnsureUserSettings
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Ensure user settings exist
            if (!$user->settings) {
                UserSetting::create([
                    'user_id' => $user->id,
                    'dark_mode' => true,
                    'notification_settings' => [],
                ]);
                
                // Refresh the relationship
                $user->load('settings');
            }
        }

        return $next($request);
    }
}