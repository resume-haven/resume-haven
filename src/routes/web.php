<?php

declare(strict_types=1);

use App\Http\Controllers\AnalyzeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LoadResumeController;
use App\Http\Controllers\StoreResumeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/analyze', function () {
    return view('analyze');
})->name('analyze');

Route::post('/analyze', AnalyzeController::class)->name('analyze.submit');

Route::post('/profile/store', StoreResumeController::class)->name('profile.store');
Route::get('/profile/load/{token}', LoadResumeController::class)->name('profile.load');

// Legal Pages
Route::get('/impressum', [LegalController::class, 'impressum'])->name('legal.impressum');
Route::get('/datenschutz', [LegalController::class, 'datenschutz'])->name('legal.datenschutz');
Route::get('/lizenzen', [LegalController::class, 'lizenzen'])->name('legal.lizenzen');

// Contact
Route::get('/kontakt', [ContactController::class, 'show'])->name('contact.show');
Route::post('/kontakt', [ContactController::class, 'submit'])->name('contact.submit');
