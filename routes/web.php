<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminResumeController;
use App\Http\Controllers\Admin\AdminUserController;

Route::get('/', fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('welcome'));

Route::get('/docs/openapi.yaml', function () {
	return response()->file(
		base_path('docs/openapi.yaml'),
		['Content-Type' => 'application/yaml']
	);
});

Route::get('/docs/swagger', fn () => redirect('/docs/swagger/index.html'));

Route::middleware(['auth', 'verified'])
	->prefix('admin')
	->name('admin.')
	->group(function (): void {
		Route::get('/', AdminDashboardController::class)->name('dashboard');
		Route::get('/resumes', [AdminResumeController::class, 'index'])->name('resumes.index');
		Route::get('/resumes/{id}', [AdminResumeController::class, 'show'])->name('resumes.show');
		Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
	});
