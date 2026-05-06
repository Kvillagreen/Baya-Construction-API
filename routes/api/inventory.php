<?php

use App\Http\Controllers\Api\Inventory\InventoryItemController;
use App\Http\Controllers\Api\Inventory\InventoryTransactionController;
use App\Http\Controllers\Api\Business\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'endpoint.permission'])->group(function (): void {
    Route::get('/inventory/items', [InventoryItemController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/items', [InventoryItemController::class, 'store'])->name('inventory.store');
    Route::patch('/inventory/items/{inventoryItem}', [InventoryItemController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/items/{inventoryItem}', [InventoryItemController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/inventory/transactions', [InventoryTransactionController::class, 'index'])->name('inventory.transactions.index');
    Route::post('/inventory/transactions', [InventoryTransactionController::class, 'store'])->name('inventory.transactions.store');
    Route::get('/inventory/analytics', [AnalyticsController::class, 'inventory'])->name('inventory.analytics');
});
