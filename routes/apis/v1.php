<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.jwt', 'scope:global'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware(['auth.jwt', 'scope:global'])->middleware('guest');
        Route::post('/refresh', [AuthController::class, 'getRefreshToken'])->withoutMiddleware('auth.jwt')->middleware('refreshable');
        Route::post('/me', [AuthController::class, 'me']);
        Route::post('/resend-verification-otp', [AuthController::class, 'sendOTP'])->withoutMiddleware('scope:global')->middleware('scope:verification')->name('resend.verification.otp');
        Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->withoutMiddleware('scope:global')->middleware('scope:verification');
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/send-otp', [AuthController::class, 'sendOTP'])->withoutMiddleware(['auth.jwt', 'scope:global'])->middleware('guest')->name('send.otp');
        Route::post('/verify-otp', [AuthController::class, 'verifyPassResetOTP'])->withoutMiddleware(['auth.jwt', 'scope:global'])->middleware('guest');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->withoutMiddleware('scope:global')->middleware('scope:pass-reset');
        Route::post('/logout', [AuthController::class, 'logout'])->withoutMiddleware(['auth.jwt', 'scope:global']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:view-user');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('permission:view-user');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create-user');
        Route::put('/', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete-user');
    });

    Route::prefix('role')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:view-role');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware('permission:view-role');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:create-role');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:update-role');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-role');
    });

    Route::prefix('user-role')->group(function () {
        Route::get('/attach/{user}/{role}', [UserRoleController::class, 'attach'])->middleware('permission:create-user-role');
        Route::get('/detach/{user}/{role}', [UserRoleController::class, 'detach'])->middleware('permission:delete-user-role');
    });

    Route::prefix('permission')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->middleware('permission:view-permission')->middleware('permission:view-permission');
        Route::get('/{permission}', [PermissionController::class, 'show'])->middleware('permission:view-permission')->middleware('permission:view-permission');
        Route::post('/', [PermissionController::class, 'store'])->middleware('permission:create-permission')->middleware('permission:create-permission');
        Route::put('/{permission}', [PermissionController::class, 'update'])->middleware('permission:update-permission')->middleware('permission:update-permission');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:delete-permission')->middleware('permission:delete-permission');
    });
});
