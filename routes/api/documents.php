<?php

use App\Http\Controllers\Api\Documents\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'endpoint.permission'])->group(function (): void {
    Route::post('/documents/presign', [DocumentController::class, 'presign'])->name('documents.presign');
});
