<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\UserModel;
use App\Infrastructure\Persistence\RoleModel;

it('redirects guests from admin to login', function () {
    $this->get('/admin')
        ->assertRedirect('/login');
});

it('rejects unverified users from admin', function () {
    $role = RoleModel::factory()->create(['name' => 'admin']);
    $user = UserModel::factory()->unverified()->create();
    $user->roles()->attach($role);

    $this->actingAs($user)
        ->get('/admin')
    ->assertRedirect('/email/verify');
});

it('allows verified users into admin', function () {
    $role = RoleModel::factory()->create(['name' => 'admin']);
    $user = UserModel::factory()->create();
    $user->roles()->attach($role);

    $this->actingAs($user)
        ->get('/admin')
        ->assertOk();
});

it('rejects verified users without admin role', function () {
    $user = UserModel::factory()->create();

    $this->actingAs($user)
        ->get('/admin')
    ->assertStatus(403);
});
