<?php

declare(strict_types=1);

use App\Http\Controllers\ResumeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/resumes/{id}', [ResumeController::class, 'show']);
Route::post('/resumes', [ResumeController::class, 'store']);

Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
