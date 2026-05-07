<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registration screen can be rendered', function (): void {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

it('registration screen shows claim context hint when resume token exists in session', function (): void {
    $response = $this->withSession([
        'resume_token' => str_repeat('T', 32),
    ])->get('/register');

    $response->assertOk();
    $response->assertSee('Analyse-Ergebnis bereit zum Zuordnen');
});

it('new users can register', function (): void {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('analyze', absolute: false));

    $user = User::query()->where('email', 'test@example.com')->first();

    expect($user)->not()->toBeNull();
    expect($user?->role)->toBe(UserRole::User);
});

it('new users are redirected to result view when resume token exists in session', function (): void {
    $response = $this->withSession([
        'resume_token' => str_repeat('T', 32),
    ])->post('/register', [
        'name' => 'Result User',
        'email' => 'result@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('result.show', absolute: false));
});
