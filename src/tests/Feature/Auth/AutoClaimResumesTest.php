<?php

declare(strict_types=1);

use App\Models\StoredResume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('auto-claims stored resume on login when resume_token exists in session', function (): void {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $token = str_repeat('T', 32);

    StoredResume::query()->create([
        'token' => $token,
        'user_id' => null,
        'encrypted_cv' => 'encrypted-cv-payload',
        'last_accessed_at' => null,
    ]);

    $response = $this->withSession(['resume_token' => $token])->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('analyze', absolute: false));
    $response->assertSessionHas('resume_claimed', true);

    expect(StoredResume::query()->where('token', $token)->value('user_id'))->toBe($user->id);
});

it('does not fail on login without resume_token in session', function (): void {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('analyze', absolute: false));
    expect(StoredResume::query()->count())->toBe(0);
});
