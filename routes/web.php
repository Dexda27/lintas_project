<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CableController;
use App\Http\Controllers\JointClosureController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\UserController; // Add this import
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Cable management routes
    Route::resource('cables', CableController::class);
    Route::get('/cables/{cable}/cores', [CableController::class, 'cores'])->name('cables.cores');
    Route::put('/cores/{core}', [CableController::class, 'updateCore'])->name('cores.update');

    // AJAX routes for cable management
    Route::get('/cables/{cable}/tubes', [CableController::class, 'getTubes'])->name('cables.tubes');
    Route::get('/cables/{cable}/tubes/{tube}/cores/available', [CableController::class, 'getAvailableCores'])->name('cables.cores.available');

    // Joint closure routes
    Route::resource('closures', JointClosureController::class);
    Route::get('/closures/{closure}/connections', [JointClosureController::class, 'connections'])->name('closures.connections');
    Route::post('/closures/{closure}/connect', [JointClosureController::class, 'connectCores'])->name('closures.connect');
    Route::get('/{closure}/edit', [JointClosureController::class, 'edit'])->name('edit');
    Route::delete('/connections/{connection}', [JointClosureController::class, 'disconnectCores'])->name('connections.disconnect');

    // Connection routes
    Route::post('/connections', [ConnectionController::class, 'store'])->name('connections.store');

    // AJAX endpoints for connection form
    Route::get('/connections/joint-closures', [ConnectionController::class, 'getJointClosures'])->name('connections.joint-closures');
    Route::get('/connections/joint-closures/{closure}/cables', [ConnectionController::class, 'getCablesByJointClosure'])->name('connections.joint-closures.cables');
    Route::get('/connections/cables/{cable}/tubes', [ConnectionController::class, 'getTubesByCable'])->name('connections.cables.tubes');
    Route::get('/connections/cables/{cable}/tubes/{tube}/cores', [ConnectionController::class, 'getAvailableCores'])->name('connections.cables.cores');

    // New routes for enhanced connection form (Cable -> Tube -> Core flow)
    Route::get('/cables/{cable}/tubes-data', [ConnectionController::class, 'getTubesByCable'])->name('cables.tubes.data');
    Route::get('/cables/{cable}/tubes/{tube}/cores-data', [ConnectionController::class, 'getCoresByTube'])->name('cables.cores.data');

    // User management routes (only accessible by super admin)
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});
