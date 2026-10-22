<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('monitor-management', fn (Request $request) => Limit::perMinute(60)->by((string) ($request->user()?->id ?? $request->ip())));

        Queue::before(function ($event): void {
            Log::withContext(['job_id' => $event->job->getJobId(), 'job_name' => $event->job->resolveName()]);
            Log::info('queue.job.started', ['queue' => $event->job->getQueue()]);
        });
        Queue::after(function ($event): void {
            Log::info('queue.job.completed', ['job_name' => $event->job->resolveName()]);
        });
        Queue::exceptionOccurred(function ($event): void {
            Log::error('queue.job.failed', ['job_name' => $event->job->resolveName(), 'exception' => $event->exception->getMessage()]);
        });
    }
}
