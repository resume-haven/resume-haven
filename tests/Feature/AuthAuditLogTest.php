<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\UserModel;
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
