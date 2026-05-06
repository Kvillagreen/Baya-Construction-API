<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'application' => 'Baya Construction Backend',
        'status' => 'ok',
        'version' => 'v1',
    ]);
});
