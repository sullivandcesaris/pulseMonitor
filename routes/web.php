<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('monitors', fn () => Inertia::render('Monitors/Index'))->name('monitors.index');
    Route::get('incidents', fn () => Inertia::render('Incidents/Index'))->name('incidents.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
