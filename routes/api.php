<?php

declare(strict_types=1);

use App\Http\Controllers\ResumeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/resumes/{id}', [ResumeController::class, 'show']);
Route::post('/resumes', [ResumeController::class, 'store']);
Route::put('/resumes/{id}', [ResumeController::class, 'update']);
Route::patch('/resumes/{id}', [ResumeController::class, 'update']);
Route::delete('/resumes/{id}', [ResumeController::class, 'destroy']);

Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::patch('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
