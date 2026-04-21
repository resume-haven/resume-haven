<?php

declare(strict_types=1);

use App\Domains\Profile\Repositories\ProfileRepository;
use App\Models\StoredResume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Carbon::setTestNow('2026-04-20 12:00:00');
});

afterEach(function (): void {
    Carbon::setTestNow();
});

test('profile repository stores resume and resolves token existence', function (): void {
    $repository = new ProfileRepository();
    $token = str_repeat('A', 32);

    expect($repository->existsByToken($token))->toBeFalse();

    $repository->store($token, 'encrypted-cv-payload');

    expect($repository->existsByToken($token))->toBeTrue();

    $stored = $repository->getByToken($token);

    expect($stored)->not->toBeNull();
    expect($stored?->encrypted_cv)->toBe('encrypted-cv-payload');
});

test('profile repository touches and deletes by token', function (): void {
    $repository = new ProfileRepository();
    $token = str_repeat('B', 32);

    $repository->store($token, 'encrypted-cv-payload');

    expect($repository->getByToken($token)?->last_accessed_at)->toBeNull();

    $repository->touchLastAccessedByToken($token);

    $stored = $repository->getByToken($token);

    expect($stored?->last_accessed_at)->not->toBeNull();
    expect($stored?->last_accessed_at?->equalTo(Carbon::now()))->toBeTrue();

    $repository->deleteByToken($token);

    expect($repository->getByToken($token))->toBeNull();
});

test('profile repository considers last accessed timestamp for expiration', function (): void {
    config(['profile.resume_retention_hours' => 24]);

    StoredResume::query()->create([
        'token' => str_repeat('C', 32),
        'encrypted_cv' => 'encrypted',
        'last_accessed_at' => Carbon::now()->subHours(26),
    ]);

    $stored = StoredResume::query()->where('token', str_repeat('C', 32))->first();

    expect($stored)->not->toBeNull();
    expect((new ProfileRepository())->isExpired($stored))->toBeTrue();
});

test('profile repository falls back to created_at when last_accessed_at is null', function (): void {
    config(['profile.resume_retention_hours' => 24]);

    StoredResume::query()->insert([
        'token' => str_repeat('D', 32),
        'encrypted_cv' => 'encrypted',
        'last_accessed_at' => null,
        'created_at' => Carbon::now()->subHours(2),
        'updated_at' => Carbon::now()->subHours(2),
    ]);

    $stored = StoredResume::query()->where('token', str_repeat('D', 32))->first();

    expect($stored)->not->toBeNull();
    expect((new ProfileRepository())->isExpired($stored))->toBeFalse();
});

test('profile repository prunes expired records for both retention branches', function (): void {
    config(['profile.resume_retention_hours' => 24]);

    StoredResume::query()->insert([
        [
            'token' => str_repeat('E', 32),
            'encrypted_cv' => 'old-access',
            'last_accessed_at' => Carbon::now()->subHours(30),
            'created_at' => Carbon::now()->subHours(40),
            'updated_at' => Carbon::now()->subHours(40),
        ],
        [
            'token' => str_repeat('F', 32),
            'encrypted_cv' => 'old-created',
            'last_accessed_at' => null,
            'created_at' => Carbon::now()->subHours(30),
            'updated_at' => Carbon::now()->subHours(30),
        ],
        [
            'token' => str_repeat('G', 32),
            'encrypted_cv' => 'fresh',
            'last_accessed_at' => Carbon::now()->subHours(2),
            'created_at' => Carbon::now()->subHours(3),
            'updated_at' => Carbon::now()->subHours(3),
        ],
    ]);

    $deleted = (new ProfileRepository())->pruneExpired();

    expect($deleted)->toBe(2);
    expect(StoredResume::query()->pluck('token')->all())->toBe([str_repeat('G', 32)]);
});

test('profile repository enforces minimum retention hours when config is lower than one', function (): void {
    config(['profile.resume_retention_hours' => 0]);

    StoredResume::query()->insert([
        'token' => str_repeat('H', 32),
        'encrypted_cv' => 'expired-by-min-retention',
        'last_accessed_at' => null,
        'created_at' => Carbon::now()->subHours(2),
        'updated_at' => Carbon::now()->subHours(2),
    ]);

    $deleted = (new ProfileRepository())->pruneExpired();

    expect($deleted)->toBe(1);
});

test('profile repository uses default retention when config is not numeric', function (): void {
    config(['profile.resume_retention_hours' => 'invalid']);

    StoredResume::query()->insert([
        'token' => str_repeat('I', 32),
        'encrypted_cv' => 'should-stay',
        'last_accessed_at' => null,
        'created_at' => Carbon::now()->subHours(24),
        'updated_at' => Carbon::now()->subHours(24),
    ]);

    $deleted = (new ProfileRepository())->pruneExpired();

    expect($deleted)->toBe(0);
    expect(StoredResume::query()->count())->toBe(1);
});

test('profile repository behandelt den exakten cutoff nicht als abgelaufen', function (): void {
    config(['profile.resume_retention_hours' => 24]);

    StoredResume::query()->insert([
        'token' => str_repeat('J', 32),
        'encrypted_cv' => 'exact-cutoff',
        'last_accessed_at' => Carbon::now()->subHours(24),
        'created_at' => Carbon::now()->subHours(30),
        'updated_at' => Carbon::now()->subHours(30),
    ]);

    $stored = StoredResume::query()->where('token', str_repeat('J', 32))->first();

    expect($stored)->not->toBeNull();
    expect((new ProfileRepository())->isExpired($stored))->toBeFalse();
});

test('profile repository prunes records at exact cutoff not away', function (): void {
    config(['profile.resume_retention_hours' => 24]);

    StoredResume::query()->insert([
        [
            'token' => str_repeat('K', 32),
            'encrypted_cv' => 'exact-last-access',
            'last_accessed_at' => Carbon::now()->subHours(24),
            'created_at' => Carbon::now()->subHours(30),
            'updated_at' => Carbon::now()->subHours(30),
        ],
        [
            'token' => str_repeat('L', 32),
            'encrypted_cv' => 'exact-created-at',
            'last_accessed_at' => null,
            'created_at' => Carbon::now()->subHours(24),
            'updated_at' => Carbon::now()->subHours(24),
        ],
    ]);

    $deleted = (new ProfileRepository())->pruneExpired();

    expect($deleted)->toBe(0);
    expect(StoredResume::query()->pluck('token')->all())->toBe([str_repeat('K', 32), str_repeat('L', 32)]);
});

test('profile repository claims token for user and keeps existing owner untouched', function (): void {
    $repository = new ProfileRepository();
    $user = User::factory()->create();
    $other = User::factory()->create();

    StoredResume::query()->insert([
        [
            'token' => str_repeat('M', 32),
            'user_id' => null,
            'encrypted_cv' => 'claim-me',
            'last_accessed_at' => null,
            'created_at' => Carbon::now()->subHour(),
            'updated_at' => Carbon::now()->subHour(),
        ],
        [
            'token' => str_repeat('N', 32),
            'user_id' => $other->id,
            'encrypted_cv' => 'already-owned',
            'last_accessed_at' => null,
            'created_at' => Carbon::now()->subHour(),
            'updated_at' => Carbon::now()->subHour(),
        ],
    ]);

    $repository->claimByToken(str_repeat('M', 32), $user->id);
    $repository->claimByToken(str_repeat('N', 32), $user->id);

    expect(StoredResume::query()->where('token', str_repeat('M', 32))->value('user_id'))->toBe($user->id);
    expect(StoredResume::query()->where('token', str_repeat('N', 32))->value('user_id'))->toBe($other->id);
});

test('profile repository returns resumes by user ordered by newest update first', function (): void {
    $repository = new ProfileRepository();
    $user = User::factory()->create();
    $other = User::factory()->create();

    StoredResume::query()->insert([
        [
            'token' => str_repeat('O', 32),
            'user_id' => $user->id,
            'encrypted_cv' => 'older',
            'last_accessed_at' => null,
            'created_at' => Carbon::now()->subHours(3),
            'updated_at' => Carbon::now()->subHours(3),
        ],
        [
            'token' => str_repeat('P', 32),
            'user_id' => $user->id,
            'encrypted_cv' => 'newer',
            'last_accessed_at' => null,
            'created_at' => Carbon::now()->subHour(),
            'updated_at' => Carbon::now()->subHour(),
        ],
        [
            'token' => str_repeat('Q', 32),
            'user_id' => $other->id,
            'encrypted_cv' => 'foreign',
            'last_accessed_at' => null,
            'created_at' => Carbon::now()->subHour(),
            'updated_at' => Carbon::now()->subHour(),
        ],
    ]);

    $tokens = $repository->getByUser($user->id)->pluck('token')->all();

    expect($tokens)->toBe([str_repeat('P', 32), str_repeat('O', 32)]);
});

test('profile repository prune keeps claimed resumes even if stale', function (): void {
    config(['profile.resume_retention_hours' => 24]);

    $user = User::factory()->create();

    StoredResume::query()->insert([
        [
            'token' => str_repeat('R', 32),
            'user_id' => $user->id,
            'encrypted_cv' => 'claimed-stale',
            'last_accessed_at' => Carbon::now()->subHours(40),
            'created_at' => Carbon::now()->subHours(50),
            'updated_at' => Carbon::now()->subHours(50),
        ],
        [
            'token' => str_repeat('S', 32),
            'user_id' => null,
            'encrypted_cv' => 'guest-stale',
            'last_accessed_at' => Carbon::now()->subHours(40),
            'created_at' => Carbon::now()->subHours(50),
            'updated_at' => Carbon::now()->subHours(50),
        ],
    ]);

    $deleted = (new ProfileRepository())->pruneExpired();

    expect($deleted)->toBe(1);
    expect(StoredResume::query()->pluck('token')->all())->toBe([str_repeat('R', 32)]);
});
