<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('welcome'));
 
Route::get('/docs/openapi.yaml', function () {
	return response()->file(
		base_path('docs/openapi.yaml'),
		['Content-Type' => 'application/yaml']
	);
});

Route::get('/docs/swagger', fn () => redirect('/docs/swagger/index.html'));
