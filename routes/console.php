<?php

use App\Jobs\CheckMonitor;
use App\Models\Monitor;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('monitors:dispatch-due', function (): void {
    Monitor::query()
        ->where('is_enabled', true)
        ->where(fn ($query) => $query->whereNull('next_check_at')->orWhere('next_check_at', '<=', now()))
        ->eachById(fn (Monitor $monitor) => CheckMonitor::dispatch($monitor));
})->purpose('Dispatch checks for monitors that are due');

Schedule::command('monitors:dispatch-due')->everyMinute();
