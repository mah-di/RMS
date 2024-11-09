<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware('auth.jwt');
        Route::post('/refresh', [AuthController::class, 'getRefreshToken'])->withoutMiddleware('auth.jwt');
        Route::post('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::prefix('role')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:view-role');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware('permission:view-role');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:create-role');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:update-role');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-role');
    });

});
