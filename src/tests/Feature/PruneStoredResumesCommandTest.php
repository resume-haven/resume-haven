<?php

declare(strict_types=1);

use App\Models\StoredResume;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('profile:prune-stored-resumes loescht abgelaufene Datensaetze', function () {
    config(['profile.resume_retention_hours' => 24]);

    DB::table('stored_resumes')->insert([
        [
            'token' => str_repeat('A', 32),
            'encrypted_cv' => 'old-encrypted',
            'last_accessed_at' => now()->subHours(30),
            'created_at' => now()->subHours(40),
            'updated_at' => now()->subHours(40),
        ],
        [
            'token' => str_repeat('B', 32),
            'encrypted_cv' => 'fresh-encrypted',
            'last_accessed_at' => now()->subHours(3),
            'created_at' => now()->subHours(4),
            'updated_at' => now()->subHours(4),
        ],
    ]);

    expect(StoredResume::query()->count())->toBe(2);

    $this->artisan('profile:prune-stored-resumes')
        ->expectsOutput('Pruned 1 expired stored resume entry.')
        ->assertExitCode(0);

    expect(StoredResume::query()->count())->toBe(1);
    expect(StoredResume::query()->first()?->token)->toBe(str_repeat('B', 32));
});

test('profile:prune-stored-resumes gibt Nachricht aus wenn nichts zu loeschen ist', function () {
    config(['profile.resume_retention_hours' => 24]);

    DB::table('stored_resumes')->insert([
        'token' => str_repeat('C', 32),
        'encrypted_cv' => 'fresh-encrypted',
        'last_accessed_at' => now()->subHours(2),
        'created_at' => now()->subHours(3),
        'updated_at' => now()->subHours(3),
    ]);

    $this->artisan('profile:prune-stored-resumes')
        ->expectsOutput('No expired stored resumes found.')
        ->assertExitCode(0);

    expect(StoredResume::query()->count())->toBe(1);
});
