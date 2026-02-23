<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/analyze', function () {
    return view('analyze');
});

Route::post('/analyze', [\App\Http\Controllers\AnalyzeController::class, 'analyze']);
