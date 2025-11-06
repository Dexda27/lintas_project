<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CableController;
use App\Http\Controllers\JointClosureController;
use App\Http\Controllers\SplitterController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\SvlanController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\CvlanController;
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

    Route::middleware(['auth'])->group(function () {
        // Splitter Routes
        Route::resource('splitters', SplitterController::class);
    });

    // User management routes (only accessible by super admin)
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // --- Node ID---
    Route::resource('nodes', NodeController::class);
    Route::get('nodes-generate-sample', [NodeController::class, 'generateSampleData'])->name('nodes.generateSample');

    // --- SVLAN ---
    Route::get('/svlan', [SvlanController::class, 'index'])->name('svlan.index');
    Route::get('/svlan/create', [SvlanController::class, 'create'])->name('svlan.create');
    Route::post('/svlan', [SvlanController::class, 'store'])->name('svlan.store');
    Route::get('/svlan/{svlan}/edit', [SvlanController::class, 'edit'])->name('svlan.edit');
    Route::put('/svlan/{svlan}', [SvlanController::class, 'update'])->name('svlan.update');
    Route::delete('/svlan/{svlan}', [SvlanController::class, 'destroy'])->name('svlan.destroy');
    Route::get('/svlan/export-all', [App\Http\Controllers\SvlanController::class, 'exportAll'])->name('svlan.exportAll');

    // --- CVLAN (GLOBAL / STANDALONE) ---
    Route::get('/cvlans', [CvlanController::class, 'all'])->name('cvlan.all'); // URL diubah ke /cvlans agar konsisten
    Route::get('/cvlans/create', [CvlanController::class, 'createall'])->name('cvlan.createall');
    Route::post('/cvlans', [CvlanController::class, 'storeAll'])->name('cvlan.storeAll');
    Route::get('/cvlans/{id}/edit', [CvlanController::class, 'editAll'])->name('cvlan.editall');
    Route::put('/cvlans/{id}', [CvlanController::class, 'updateAll'])->name('cvlan.updateall');
    Route::delete('/cvlans/{id}', [CvlanController::class, 'destroyAll'])->name('cvlan.destroyall');
    Route::get('/cvlan/export-all', [CvlanController::class, 'exportAllCsv'])->name('cvlan.exportAll');

    // --- CVLAN (NESTED UNDER SVLAN) ---
    Route::get('/svlan/{svlan_id}/cvlans', [CvlanController::class, 'index'])->name('cvlan.index');
    Route::get('/svlan/{svlan_id}/cvlans/create', [CvlanController::class, 'create'])->name('cvlan.create');
    Route::post('/svlan/{svlan_id}/cvlans', [CvlanController::class, 'store'])->name('cvlan.store');
    Route::get('/svlan/{svlan_id}/cvlans/{id}/edit', [CvlanController::class, 'edit'])->name('cvlan.edit');
    Route::put('/svlan/{svlan_id}/cvlans/{id}', [CvlanController::class, 'update'])->name('cvlan.update');
    Route::delete('/svlan/{svlan_id}/cvlans/{id}', [CvlanController::class, 'destroy'])->name('cvlan.destroy');
    Route::get('/svlan/{svlan_id}/cvlans/export', [CvlanController::class, 'exportCsvForSvlan'])->name('cvlan.exportForSvlan');

});
