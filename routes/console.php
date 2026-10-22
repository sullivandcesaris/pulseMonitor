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
    $dispatched = 0;
    Monitor::query()
        ->where('is_enabled', true)
        ->where(fn ($query) => $query->whereNull('next_check_at')->orWhere('next_check_at', '<=', now()))
        ->eachById(function (Monitor $monitor) use (&$dispatched): void {
            $claimed = Monitor::query()
                ->whereKey($monitor->id)
                ->where('is_enabled', true)
                ->where(fn ($query) => $query->whereNull('next_check_at')->orWhere('next_check_at', '<=', now()))
                ->update(['next_check_at' => now()->addSeconds($monitor->interval_seconds)]);

            if ($claimed === 1) {
                CheckMonitor::dispatch($monitor);
                $dispatched++;
            }
        });
    $this->info("Dispatched {$dispatched} monitor checks.");
})->purpose('Dispatch checks for monitors that are due');

Schedule::command('monitors:dispatch-due')->everyMinute();
