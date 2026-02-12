<?php

declare(strict_types=1);

use App\Domain\Events\UserCreatedEvent;
use App\Domain\Events\UserDeletedEvent;
use App\Domain\Events\UserUpdatedEvent;
use App\Infrastructure\Persistence\UserModel;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

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

it('sends verification email after user registration', function () {
    Notification::fake();

    $payload = [
        'name' => 'Verify User',
        'email' => 'verify@example.com',
        'password' => 'password123',
    ];

    $this->postJson('/api/users', $payload)
        ->assertCreated();

    $user = UserModel::query()->where('email', $payload['email'])->first();
    expect($user)->not->toBeNull();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('resends verification email for unverified users', function () {
    Notification::fake();

    $user = UserModel::factory()->unverified()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/email/verification-notification')
        ->assertStatus(202)
        ->assertJson(['message' => 'Verification email sent.']);

    Notification::assertSentTo($user, VerifyEmail::class);

    $this->assertDatabaseHas('auth_audit_logs', [
        'user_id' => $user->id,
        'event' => 'auth.verification.resent',
    ]);
});

it('does not resend verification email for verified users', function () {
    Notification::fake();

    $user = UserModel::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/email/verification-notification')
        ->assertOk()
        ->assertJson(['message' => 'Email already verified.']);

    Notification::assertNothingSent();
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

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", $payload)
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

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", $payload)
        ->assertOk();

    $updated = UserModel::query()->find($user->id);
    expect(Hash::check('oldpassword', (string) $updated?->password))->toBeTrue();

    Event::assertDispatched(UserUpdatedEvent::class);
});

it('rejects updating another user without admin role', function () {
    $actor = UserModel::factory()->create();
    $target = UserModel::factory()->create();

    $this->actingAs($actor)
        ->putJson("/api/users/{$target->id}", [
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
        ])
        ->assertStatus(403);
});

it('allows admin to update another user', function () {
    $role = \App\Infrastructure\Persistence\RoleModel::factory()->create(['name' => 'admin']);
    $actor = UserModel::factory()->create();
    $actor->roles()->attach($role);
    $target = UserModel::factory()->create();

    $this->actingAs($actor)
        ->putJson("/api/users/{$target->id}", [
            'name' => 'Admin Update',
            'email' => 'admin-update@example.com',
        ])
        ->assertOk();
});

it('rejects updates from unverified users', function () {
    $user = UserModel::factory()->unverified()->create();

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", [
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
        ])
        ->assertStatus(403);
});

it('patches a user email', function () {
    Event::fake();

    $user = UserModel::factory()->create([
        'name' => 'Old User',
        'email' => 'old@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $this->actingAs($user)
        ->patchJson("/api/users/{$user->id}", [
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

it('patches a user password', function () {
    $user = UserModel::factory()->create([
        'name' => 'Patch User',
        'email' => 'patch@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $this->actingAs($user)
        ->patchJson("/api/users/{$user->id}", [
            'password' => 'newpatchpassword',
        ])
        ->assertOk()
        ->assertJson([
            'id' => $user->id,
            'name' => 'Patch User',
            'email' => 'patch@example.com',
        ]);

    $updated = UserModel::query()->find($user->id);
    expect(Hash::check('newpatchpassword', (string) $updated?->password))->toBeTrue();
});

it('validates user patch input', function () {
    $user = UserModel::factory()->create();

    $this->actingAs($user)
        ->patchJson("/api/users/{$user->id}", [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['fields']);
});

it('returns not found for user patch', function () {
    $user = UserModel::factory()->create();

    $this->actingAs($user)
        ->patchJson('/api/users/999999', [
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

    $this->actingAs($user)
        ->deleteJson("/api/users/{$user->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);

    Event::assertDispatched(UserDeletedEvent::class);
});

it('rejects deleting another user without admin role', function () {
    $actor = UserModel::factory()->create();
    $target = UserModel::factory()->create();

    $this->actingAs($actor)
        ->deleteJson("/api/users/{$target->id}")
        ->assertStatus(403);
});

it('returns not found for user delete', function () {
    $user = UserModel::factory()->create();

    $this->actingAs($user)
        ->deleteJson('/api/users/999999')
        ->assertNotFound()
        ->assertJson([
            'message' => 'User not found.',
        ]);
});

it('returns not found for user update', function () {
    $user = UserModel::factory()->create();

    $this->actingAs($user)
        ->putJson('/api/users/999999', [
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

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", [
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
