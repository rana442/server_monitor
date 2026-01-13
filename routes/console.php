<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler (Laravel 12)
|--------------------------------------------------------------------------
*/

Schedule::command('monitors:check')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/monitor-checks.log'));

Schedule::call(function () {
    Log::info('[SCHEDULER] Heartbeat at ' . now());

    file_put_contents(
        storage_path('logs/scheduler-heartbeat.txt'),
        '[' . now() . "] Scheduler OK\n",
        FILE_APPEND
    );
})->everyMinute();
