<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\MonitorController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('monitors', MonitorController::class)->except(['show']);
    Route::get('incidents', [IncidentController::class, 'index'])->name('incidents.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
