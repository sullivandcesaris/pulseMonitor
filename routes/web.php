<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\MonitorController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('docs', function () {
    return Inertia::render('Docs');
})->name('docs');

Route::get('health', HealthController::class)
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        AddQueuedCookiesToResponse::class,
    ])
    ->name('health');
Route::get('metrics', MetricsController::class)
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        AddQueuedCookiesToResponse::class,
    ])
    ->name('metrics');

Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('monitors', MonitorController::class)->except(['show'])->middleware('throttle:monitor-management');
    Route::get('incidents', [IncidentController::class, 'index'])->name('incidents.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
