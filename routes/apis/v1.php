<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApartmentServiceChargeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseSubTypeController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\OccupantController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ResidenceController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ServiceChargeController;
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
        Route::put('/{permission}', [PermissionController::class, 'update'])->middleware('permission:update-permission')->middleware('permission:update-permission');
    });

    Route::prefix('role-permission')->group(function () {
        Route::get('/attach/{role}/{permission}', [RolePermissionController::class, 'attach'])->middleware('permission:create-role-permission');
        Route::get('/detach/{role}/{permission}', [RolePermissionController::class, 'detach'])->middleware('permission:delete-role-permission');
        Route::get('/attach-bulk/{role}/{slug}', [RolePermissionController::class, 'bulkAttach'])->middleware('permission:create-role-permission');
        Route::get('/detach-bulk/{role}/{slug}', [RolePermissionController::class, 'bulkDetach'])->middleware('permission:delete-role-permission');
    });

    Route::prefix('residence')->group(function () {
        Route::get('/', [ResidenceController::class, 'index'])->middleware('permission:view-residence');
        Route::get('/{residence}', [ResidenceController::class, 'show'])->middleware('permission:view-residence');
        Route::post('/', [ResidenceController::class, 'store'])->middleware('permission:create-residence');
        Route::put('/{residence}', [ResidenceController::class, 'update'])->middleware('permission:update-residence');
        Route::delete('/{residence}', [ResidenceController::class, 'destroy'])->middleware('permission:delete-residence');
    });

    Route::prefix('apartment')->group(function () {
        Route::get('/', [ApartmentController::class, 'index'])->middleware('permission:view-apartment');
        Route::get('/{apartment}', [ApartmentController::class, 'show'])->middleware('permission:view-apartment');
        Route::post('/', [ApartmentController::class, 'store'])->middleware('permission:create-apartment');
        Route::put('/{apartment}', [ApartmentController::class, 'update'])->middleware('permission:update-apartment');
        Route::delete('/{apartment}', [ApartmentController::class, 'destroy'])->middleware('permission:delete-apartment');
    });

    Route::prefix('occupant')->group(function () {
        Route::get('/', [OccupantController::class, 'index'])->middleware('permission:view-occupant');
        Route::get('/{occupant}', [OccupantController::class, 'show'])->middleware('permission:view-occupant');
        Route::post('/', [OccupantController::class, 'store'])->middleware('permission:create-occupant');
        Route::put('/{occupant}', [OccupantController::class, 'update'])->middleware('permission:update-occupant');
        Route::delete('/{occupant}', [OccupantController::class, 'destroy'])->middleware('permission:delete-occupant');
    });

    Route::prefix('service-charge')->group(function () {
        Route::get('/', [ServiceChargeController::class, 'index'])->middleware('permission:view-service-charge');
        Route::get('/{serviceCharge}', [ServiceChargeController::class, 'show'])->middleware('permission:view-service-charge');
        Route::post('/', [ServiceChargeController::class, 'store'])->middleware('permission:create-service-charge');
        Route::put('/{serviceCharge}', [ServiceChargeController::class, 'update'])->middleware('permission:update-service-charge');
        Route::delete('/{serviceCharge}', [ServiceChargeController::class, 'destroy'])->middleware('permission:delete-service-charge');
    });

    Route::prefix('apartment-service-charge')->group(function () {
        Route::get('/', [ApartmentServiceChargeController::class, 'index'])->middleware('permission:view-apartment-service-charge');
        Route::get('/{apartmentServiceCharge}', [ApartmentServiceChargeController::class, 'show'])->middleware('permission:view-apartment-service-charge');
        Route::post('/', [ApartmentServiceChargeController::class, 'save'])->middleware('permission:create-service-charge');
        Route::delete('/{apartmentServiceCharge}', [ApartmentServiceChargeController::class, 'destroy'])->middleware('permission:delete-apartment-service-charge');
    });

    Route::prefix('revenue')->group(function () {
        Route::get('/', [RevenueController::class, 'index'])->middleware('permission:view-revenue');
        Route::get('/{revenue}', [RevenueController::class, 'show'])->middleware('permission:view-revenue');
        Route::post('/', [RevenueController::class, 'store'])->middleware('permission:create-revenue');
        Route::put('/{revenue}', [RevenueController::class, 'update'])->middleware('permission:update-revenue');
        Route::delete('/{revenue}', [RevenueController::class, 'destroy'])->middleware('permission:delete-revenue');
    });

    Route::prefix('expense-type')->group(function () {
        Route::get('/', [ExpenseTypeController::class, 'index'])->middleware('permission:view-expense-type');
        Route::get('/{expenseType}', [ExpenseTypeController::class, 'show'])->middleware('permission:view-expense-type');
        Route::post('/', [ExpenseTypeController::class, 'store'])->middleware('permission:create-expense-type');
        Route::put('/{expenseType}', [ExpenseTypeController::class, 'update'])->middleware('permission:update-expense-type');
        Route::delete('/{expenseType}', [ExpenseTypeController::class, 'destroy'])->middleware('permission:delete-expense-type');
    });

    Route::prefix('expense-sub-type')->group(function () {
        Route::get('/', [ExpenseSubTypeController::class, 'index'])->middleware('permission:view-expense-sub-type');
        Route::get('/{expenseSubType}', [ExpenseSubTypeController::class, 'show'])->middleware('permission:view-expense-sub-type');
        Route::post('/', [ExpenseSubTypeController::class, 'store'])->middleware('permission:create-expense-sub-type');
        Route::put('/{expenseSubType}', [ExpenseSubTypeController::class, 'update'])->middleware('permission:update-expense-sub-type');
        Route::delete('/{expenseSubType}', [ExpenseSubTypeController::class, 'destroy'])->middleware('permission:delete-expense-sub-type');
    });

    Route::prefix('expense')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->middleware('permission:view-expense');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->middleware('permission:view-expense');
        Route::post('/', [ExpenseController::class, 'store'])->middleware('permission:create-expense');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->middleware('permission:update-expense');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->middleware('permission:delete-expense');
    });
});
