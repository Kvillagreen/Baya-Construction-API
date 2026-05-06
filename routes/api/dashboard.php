<?php

use App\Http\Controllers\Api\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'endpoint.permission'])->group(function (): void {
    Route::get('/dashboard/summary', DashboardController::class)->name('dashboard.summary');
});
