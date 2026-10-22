<?php

use App\Jobs\BenchmarkJob;
use App\Jobs\CheckMonitor;
use App\Models\Monitor;
use App\Models\Organization;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schedule;
use Symfony\Component\Process\Process;

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

Artisan::command('monitoring:benchmark {--sizes=100,1000,10000} {--workers=4} {--skip-queue}', function (): void {
    $sizes = array_values(array_filter(array_map('intval', explode(',', (string) $this->option('sizes')))));
    $this->table(['Monitors', 'DB query (ms)', 'CPU (ms)', 'Peak memory (MB)', 'Queue jobs/s', 'Queue wait p95 (ms)'], array_map(function (int $size): array {
        $memoryBefore = memory_get_usage(true);
        $cpuBefore = getrusage();
        DB::beginTransaction();

        try {
            $organization = Organization::create(['name' => "Benchmark {$size}", 'slug' => 'benchmark-'.uniqid()]);
            $now = now();
            $rows = [];
            for ($index = 0; $index < $size; $index++) {
                $rows[] = [
                    'organization_id' => $organization->id,
                    'name' => "Benchmark monitor {$index}",
                    'target_url' => 'https://example.com/health',
                    'method' => 'GET',
                    'interval_seconds' => 60,
                    'expected_status' => 200,
                    'timeout_seconds' => 10,
                    'is_enabled' => true,
                    'next_check_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (count($rows) === 500) {
                    DB::table('monitors')->insert($rows);
                    $rows = [];
                }
            }
            if ($rows !== []) {
                DB::table('monitors')->insert($rows);
            }

            $queryStartedAt = microtime(true);
            Monitor::query()->where('is_enabled', true)->where('next_check_at', '<=', now())->count();
            $queryMs = round((microtime(true) - $queryStartedAt) * 1000, 2);
        } finally {
            DB::rollBack();
        }
        $cpuAfter = getrusage();
        $cpuMs = round((($cpuAfter['ru_utime.tv_sec'] - $cpuBefore['ru_utime.tv_sec']) * 1000) + (($cpuAfter['ru_utime.tv_usec'] - $cpuBefore['ru_utime.tv_usec']) / 1000), 2);

        if ($this->option('skip-queue')) {
            return [$size, $queryMs, $cpuMs, round(max(memory_get_peak_usage(true), $memoryBefore) / 1024 / 1024, 2), 'skipped', 'skipped'];
        }

        $runId = uniqid('benchmark-', true);
        $key = "pulsemonitor:benchmark:{$runId}";
        $queueStartedAt = microtime(true);
        for ($index = 0; $index < $size; $index++) {
            BenchmarkJob::dispatch($runId, microtime(true));
        }
        $workers = [];
        for ($workerIndex = 0; $workerIndex < max(1, (int) $this->option('workers')); $workerIndex++) {
            $worker = Process::fromShellCommandline('php artisan queue:work redis --queue=benchmark --stop-when-empty --tries=1 --timeout=300');
            $worker->setTimeout(600);
            $worker->start();
            $workers[] = $worker;
        }
        $deadline = microtime(true) + max(120, $size / 10);
        while ((int) Redis::get("{$key}:processed") < $size && microtime(true) < $deadline) {
            usleep(10000);
        }
        foreach ($workers as $worker) {
            $worker->wait();
        }
        $waits = array_map('floatval', Redis::lrange("{$key}:waits", 0, -1));
        sort($waits);
        $p95 = $waits === [] ? null : $waits[(int) floor((count($waits) - 1) * 0.95)];
        $queueSeconds = max(microtime(true) - $queueStartedAt, 0.001);
        Redis::del("{$key}:processed", "{$key}:waits");

        return [
            $size,
            $queryMs,
            $cpuMs,
            round(max(memory_get_peak_usage(true), $memoryBefore) / 1024 / 1024, 2),
            round($size / $queueSeconds, 2),
            $p95,
        ];
    }, $sizes));
})->purpose('Benchmark database scale and Redis queue throughput with synthetic data');

Schedule::command('monitors:dispatch-due')->everyMinute();
Schedule::command('horizon:snapshot')->everyFiveMinutes();
