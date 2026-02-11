<?php

declare(strict_types=1);

use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ResumeStatusHistoryController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Support\Facades\Route;

// Public endpoints (no authentication required)
Route::get('/resumes/{id}', [ResumeController::class, 'show']);
Route::get('/resumes/{id}/status-history', ResumeStatusHistoryController::class);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);  // User registration
Route::post('/tokens', [TokenController::class, 'store']);  // Generate API token
Route::post('/tokens/revoke', [TokenController::class, 'destroy']);  // Revoke tokens
Route::post('/email/verification-notification', EmailVerificationController::class)
    ->middleware(['auth:sanctum', 'throttle:6,1']);

// Protected endpoints (authentication required)
Route::middleware(['auth:sanctum', 'verified'])->group(function (): void {
    // Resumes
    Route::post('/resumes', [ResumeController::class, 'store']);
    Route::put('/resumes/{id}', [ResumeController::class, 'update']);
    Route::patch('/resumes/{id}', [ResumeController::class, 'update']);
    Route::delete('/resumes/{id}', [ResumeController::class, 'destroy']);

    // Users (update/delete own account)
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::patch('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

});
