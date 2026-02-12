<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\UserModel;
use App\Infrastructure\Persistence\AuthAuditLogModel;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Verified;

it('logs auth login events', function () {
    $user = UserModel::factory()->create();

    event(new Login('web', $user, false));

    $this->assertDatabaseHas('auth_audit_logs', [
        'user_id' => $user->id,
        'event' => 'auth.login',
    ]);
});

it('logs auth logout events', function () {
    $user = UserModel::factory()->create();

    event(new Logout('web', $user));

    $this->assertDatabaseHas('auth_audit_logs', [
        'user_id' => $user->id,
        'event' => 'auth.logout',
    ]);
});

it('logs auth verified events', function () {
    $user = UserModel::factory()->create();

    event(new Verified($user));

    $this->assertDatabaseHas('auth_audit_logs', [
        'user_id' => $user->id,
        'event' => 'auth.verified',
    ]);
});

it('prunes auth audit logs older than 30 days', function () {
    $user = UserModel::factory()->create();

    $oldLog = AuthAuditLogModel::query()->create([
        'user_id' => $user->id,
        'event' => 'auth.login',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Agent',
        'context' => ['source' => 'test'],
    ]);

    $oldLog->forceFill([
        'created_at' => now()->subDays(31),
        'updated_at' => now()->subDays(31),
    ])->saveQuietly();

    $recentLog = AuthAuditLogModel::query()->create([
        'user_id' => $user->id,
        'event' => 'auth.logout',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Agent',
        'context' => ['source' => 'test'],
    ]);

    $recentLog->forceFill([
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ])->saveQuietly();

    $this->artisan('model:prune', [
        '--model' => AuthAuditLogModel::class,
    ])->assertExitCode(0);

    $this->assertDatabaseMissing('auth_audit_logs', ['id' => $oldLog->id]);
    $this->assertDatabaseHas('auth_audit_logs', ['id' => $recentLog->id]);
});
