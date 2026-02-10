<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows a user', function () {
    $user = UserModel::factory()->create();

    $this->getJson("/api/users/{$user->id}")
        ->assertOk()
        ->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
});

it('returns not found for missing user', function () {
    $this->getJson('/api/users/999999')
        ->assertNotFound()
        ->assertJson([
            'message' => 'User not found.',
        ]);
});

it('creates a user', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'user@example.com',
        'password' => 'password123',
    ];

    $this->postJson('/api/users', $payload)
        ->assertCreated()
        ->assertJson([
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);

    $this->assertDatabaseHas('users', [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);

    $user = UserModel::query()->where('email', $payload['email'])->first();
    expect($user)->not->toBeNull();
    expect(Hash::check($payload['password'], (string) $user?->password))->toBeTrue();
});

it('validates user creation input', function () {
    $this->postJson('/api/users', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'short',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('rejects long user email and password', function () {
    $this->postJson('/api/users', [
        'name' => 'Valid Name',
        'email' => str_repeat('a', 256) . '@example.com',
        'password' => str_repeat('p', 256),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});
