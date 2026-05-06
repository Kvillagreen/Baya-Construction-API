<?php

use App\Http\Controllers\Api\Auth\SessionController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'throttle:login', 'hidden.captcha'])->group(function (): void {
    Route::post('/auth/login', [SessionController::class, 'login'])
        ->withoutMiddleware([PreventRequestForgery::class])
        ->name('auth.login');
});

Route::middleware(['web', 'auth:sanctum'])->group(function (): void {
    Route::get('/auth/me', [SessionController::class, 'me'])->name('auth.me');
    Route::post('/auth/logout', [SessionController::class, 'logout'])
        ->withoutMiddleware([PreventRequestForgery::class])
        ->name('auth.logout');
});
