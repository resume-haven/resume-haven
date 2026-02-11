<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\UserModel;
use Illuminate\Support\Facades\Hash;

it('creates api token with valid credentials', function () {
    $user = UserModel::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/tokens', [
        'email' => 'test@example.com',
        'password' => 'password123',
        'device_name' => 'My Device',
    ])
        ->assertCreated()
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => 'test@example.com',
            ],
        ]);

    expect($response->json('token'))->toBeString()
        ->and($response->json('token'))->toContain('|');

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'My Device',
    ]);
});

it('rejects invalid email', function () {
    $this->postJson('/api/tokens', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
        'device_name' => 'My Device',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('rejects invalid password', function () {
    UserModel::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->postJson('/api/tokens', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
        'device_name' => 'My Device',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('requires device name', function () {
    UserModel::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->postJson('/api/tokens', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['device_name']);
});

it('validates email field', function () {
    $this->postJson('/api/tokens', [
        'email' => 'invalid-email',
        'password' => 'password123',
        'device_name' => 'My Device',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates password minimum length', function () {
    UserModel::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->postJson('/api/tokens', [
        'email' => 'test@example.com',
        'password' => 'short',
        'device_name' => 'My Device',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('revokes all tokens for authenticated user', function () {
    $user = UserModel::factory()->create();
    $token1 = $user->createToken('token1')->plainTextToken;
    $token2 = $user->createToken('token2')->plainTextToken;

    $this->assertDatabaseCount('personal_access_tokens', 2);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/tokens/revoke')
        ->assertOk()
        ->assertJson(['message' => 'All tokens revoked.']);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('returns unauthorized when revoking without authentication', function () {
    $this->postJson('/api/tokens/revoke')
        ->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
});

it('token can authenticate api requests', function () {
    $user = UserModel::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/users/' . $user->id)
        ->assertOk()
        ->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
});
