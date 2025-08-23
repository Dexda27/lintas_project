<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CableController;
use App\Http\Controllers\JointClosureController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('cables', CableController::class);
    Route::get('/cables/{cable}/cores', [CableController::class, 'cores'])->name('cables.cores');
    Route::put('/cores/{core}', [CableController::class, 'updateCore'])->name('cores.update');

    Route::resource('closures', JointClosureController::class);
    Route::get('/closures/{closure}/connections', [JointClosureController::class, 'connections'])->name('closures.connections');
    Route::post('/closures/{closure}/connect', [JointClosureController::class, 'connectCores'])->name('closures.connect');
    Route::delete('/connections/{connection}', [JointClosureController::class, 'disconnectCores'])->name('connections.disconnect');
});