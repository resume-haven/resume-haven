<?php

declare(strict_types=1);

use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ResumeStatusHistoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public endpoints (no authentication required)
Route::get('/resumes/{id}', [ResumeController::class, 'show']);
Route::get('/resumes/{id}/status-history', ResumeStatusHistoryController::class);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);  // User registration

// Protected endpoints (authentication required)
Route::middleware(['auth:sanctum'])->group(function (): void {
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
