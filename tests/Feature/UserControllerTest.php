<?php

declare(strict_types=1);

use App\Domain\Events\UserCreatedEvent;
use App\Domain\Events\UserDeletedEvent;
use App\Domain\Events\UserUpdatedEvent;
use App\Infrastructure\Persistence\UserModel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

it('shows a user', function () {
    $user = UserModel::factory()->create();

    $this->getJson("/api/users/{$user->id}")
        ->assertOk()
        ->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->toISOString(),
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
    Event::fake();

    $payload = [
        'name' => 'Test User',
        'email' => 'user@example.com',
        'password' => 'password123',
    ];

    $response = $this->postJson('/api/users', $payload)
        ->assertCreated();

    $this->assertDatabaseHas('users', [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);

    $user = UserModel::query()->where('email', $payload['email'])->first();
    expect($user)->not->toBeNull();
    expect(Hash::check($payload['password'], (string) $user?->password))->toBeTrue();

    $response->assertJson([
        'name' => $payload['name'],
        'email' => $payload['email'],
        'created_at' => $user?->created_at?->toISOString(),
    ]);

    Event::assertDispatched(UserCreatedEvent::class);
});

it('updates a user with password', function () {
    Event::fake();

    $user = UserModel::factory()->create([
        'name' => 'Old User',
        'email' => 'old@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $payload = [
        'name' => 'Updated User',
        'email' => 'updated@example.com',
        'password' => 'newpassword123',
    ];

    $this->putJson("/api/users/{$user->id}", $payload)
        ->assertOk()
        ->assertJson([
            'id' => $user->id,
            'name' => $payload['name'],
            'email' => $payload['email'],
            'created_at' => $user->created_at->toISOString(),
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);

    $updated = UserModel::query()->find($user->id);
    expect(Hash::check($payload['password'], (string) $updated?->password))->toBeTrue();

    Event::assertDispatched(UserUpdatedEvent::class);
});

it('updates a user without password', function () {
    Event::fake();

    $user = UserModel::factory()->create([
        'name' => 'Old User',
        'email' => 'old@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $payload = [
        'name' => 'Updated User',
        'email' => 'updated@example.com',
    ];

    $this->putJson("/api/users/{$user->id}", $payload)
        ->assertOk();

    $updated = UserModel::query()->find($user->id);
    expect(Hash::check('oldpassword', (string) $updated?->password))->toBeTrue();

    Event::assertDispatched(UserUpdatedEvent::class);
});

it('patches a user email', function () {
    Event::fake();

    $user = UserModel::factory()->create([
        'name' => 'Old User',
        'email' => 'old@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $this->patchJson("/api/users/{$user->id}", [
        'email' => 'patched@example.com',
    ])
        ->assertOk()
        ->assertJson([
            'id' => $user->id,
            'name' => 'Old User',
            'email' => 'patched@example.com',
            'created_at' => $user->created_at->toISOString(),
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'patched@example.com',
    ]);

    Event::assertDispatched(UserUpdatedEvent::class);
});

it('validates user patch input', function () {
    $user = UserModel::factory()->create();

    $this->patchJson("/api/users/{$user->id}", [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['fields']);
});

it('returns not found for user patch', function () {
    $this->patchJson('/api/users/999999', [
        'email' => 'patched@example.com',
    ])
        ->assertNotFound()
        ->assertJson([
            'message' => 'User not found.',
        ]);
});

it('deletes a user', function () {
    Event::fake();

    $user = UserModel::factory()->create();

    $this->deleteJson("/api/users/{$user->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);

    Event::assertDispatched(UserDeletedEvent::class);
});

it('returns not found for user delete', function () {
    $this->deleteJson('/api/users/999999')
        ->assertNotFound()
        ->assertJson([
            'message' => 'User not found.',
        ]);
});

it('returns not found for user update', function () {
    $this->putJson('/api/users/999999', [
        'name' => 'Missing User',
        'email' => 'missing@example.com',
    ])
        ->assertNotFound()
        ->assertJson([
            'message' => 'User not found.',
        ]);
});

it('validates user update input', function () {
    $user = UserModel::factory()->create();

    $this->putJson("/api/users/{$user->id}", [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'short',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
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
