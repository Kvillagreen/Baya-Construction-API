<?php

use App\Http\Controllers\Api\Identity\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'endpoint.permission'])->group(function (): void {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
