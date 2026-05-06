<?php

use App\Http\Controllers\Api\Sales\OpportunityController;
use App\Http\Controllers\Api\Sales\BusinessSaleController;
use App\Http\Controllers\Api\Business\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'endpoint.permission'])->group(function (): void {
    Route::get('/sales/opportunities', [OpportunityController::class, 'index'])->name('sales.index');
    Route::post('/sales/opportunities', [OpportunityController::class, 'store'])->name('sales.store');
    Route::patch('/sales/opportunities/{opportunity}', [OpportunityController::class, 'update'])->name('sales.update');
    Route::delete('/sales/opportunities/{opportunity}', [OpportunityController::class, 'destroy'])->name('sales.destroy');
    Route::get('/sales/records', [BusinessSaleController::class, 'index'])->name('sales.records.index');
    Route::post('/sales/records', [BusinessSaleController::class, 'store'])->name('sales.records.store');
    Route::get('/sales/analytics', [AnalyticsController::class, 'sales'])->name('sales.analytics');
});
