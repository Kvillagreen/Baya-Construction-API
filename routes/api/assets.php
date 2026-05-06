<?php

use App\Http\Controllers\Api\Assets\AssetController;
use App\Http\Controllers\Api\Assets\AssetAssignmentController;
use App\Http\Controllers\Api\Business\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'endpoint.permission'])->group(function (): void {
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::patch('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    Route::get('/assets/assignments', [AssetAssignmentController::class, 'index'])->name('assets.assignments.index');
    Route::post('/assets/{asset}/assign', [AssetAssignmentController::class, 'assign'])->name('assets.assign');
    Route::post('/assets/{asset}/borrow', [AssetAssignmentController::class, 'borrow'])->name('assets.borrow');
    Route::get('/assets/analytics', [AnalyticsController::class, 'assets'])->name('assets.analytics');
});
