<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware('auth.jwt');
    Route::post('/refresh', [AuthController::class, 'getRefreshToken']);
    Route::post('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
