<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = ['database' => false, 'redis' => false];

        try {
            $connection = config('database.default');
            if ($connection !== 'sqlite') {
                $socket = @fsockopen(config("database.connections.{$connection}.host"), (int) config("database.connections.{$connection}.port"), $errorCode, $errorMessage, 2);
                if (is_resource($socket)) {
                    fclose($socket);
                } else {
                    throw new \RuntimeException($errorMessage ?: "Database port unavailable ({$errorCode}).");
                }
            }
            DB::select('select 1');
            $checks['database'] = true;
        } catch (Throwable) {
            //
        }

        try {
            Redis::connection()->ping();
            $checks['redis'] = true;
        } catch (Throwable) {
            //
        }

        $healthy = ! in_array(false, $checks, true);

        return response()->json(['status' => $healthy ? 'ok' : 'degraded', 'checks' => $checks], $healthy ? 200 : 503);
    }
}
