<?php

use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MetadataController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TokenController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->as('api.v1.')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:api-auth')
        ->name('auth.login');
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('throttle:api-password-reset')
        ->name('auth.forgot-password');
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:api-password-reset')
        ->name('auth.reset-password');

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('me', [ProfileController::class, 'show'])
            ->middleware('abilities:profile:read')
            ->name('me.show');
        Route::patch('me', [ProfileController::class, 'update'])
            ->middleware('abilities:profile:write')
            ->name('me.update');
        Route::put('me/password', [ProfileController::class, 'updatePassword'])
            ->middleware('abilities:profile:write')
            ->name('me.password');
        Route::delete('me', [ProfileController::class, 'destroy'])
            ->middleware('abilities:profile:write')
            ->name('me.destroy');

        Route::get('metadata', MetadataController::class)->name('metadata');

        Route::get('tokens', [TokenController::class, 'index'])
            ->middleware(['abilities:tokens:manage', 'throttle:api-tokens'])
            ->name('tokens.index');
        Route::post('tokens', [TokenController::class, 'store'])
            ->middleware(['abilities:tokens:manage', 'throttle:api-tokens'])
            ->name('tokens.store');
        Route::delete('tokens/{token}', [TokenController::class, 'destroy'])
            ->middleware(['abilities:tokens:manage', 'throttle:api-tokens'])
            ->name('tokens.destroy');

        Route::apiResource('accounts', AccountController::class)
            ->only(['index', 'show'])
            ->middleware('abilities:accounts:read');
        Route::apiResource('accounts', AccountController::class)
            ->only(['store'])
            ->middleware(['abilities:accounts:write', 'idempotency']);
        Route::apiResource('accounts', AccountController::class)
            ->only(['update', 'destroy'])
            ->middleware('abilities:accounts:write');

        Route::apiResource('transactions', TransactionController::class)
            ->only(['index', 'show'])
            ->middleware('abilities:transactions:read');
        Route::apiResource('transactions', TransactionController::class)
            ->only(['store'])
            ->middleware(['abilities:transactions:write', 'idempotency']);
        Route::apiResource('transactions', TransactionController::class)
            ->only(['update', 'destroy'])
            ->middleware('abilities:transactions:write');
    });
});
