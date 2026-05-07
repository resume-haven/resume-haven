<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('login screen can be rendered', function (): void {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('login screen shows claim context hint when resume token exists in session', function (): void {
    $response = $this->withSession([
        'resume_token' => str_repeat('T', 32),
    ])->get('/login');

    $response->assertOk();
    $response->assertSee('Analyse-Ergebnis bereit zum Zuordnen');
});

it('users can authenticate using the login screen', function (): void {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('analyze', absolute: false));
});

it('users are redirected to result view when resume token exists in session', function (): void {
    $user = User::factory()->create();

    $response = $this->withSession([
        'resume_token' => str_repeat('T', 32),
    ])->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('result.show', absolute: false));
});

it('users can not authenticate with invalid password', function (): void {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

it('users can logout', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
