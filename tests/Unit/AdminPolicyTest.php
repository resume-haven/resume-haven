<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\RoleModel;
use App\Infrastructure\Persistence\UserModel;
use App\Policies\AdminPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('allows admin access to admin policy abilities', function () {
    $policy = new AdminPolicy();
    $role = RoleModel::factory()->create(['name' => 'admin']);
    $user = UserModel::factory()->create();
    $user->roles()->attach($role);

    expect($policy->dashboard($user))->toBeTrue();
    expect($policy->viewUsers($user))->toBeTrue();
    expect($policy->viewResumes($user))->toBeTrue();
    expect($policy->viewResume($user))->toBeTrue();
});

it('denies non-admin access to admin policy abilities', function () {
    $policy = new AdminPolicy();
    $user = UserModel::factory()->create();

    expect($policy->dashboard($user))->toBeFalse();
    expect($policy->viewUsers($user))->toBeFalse();
    expect($policy->viewResumes($user))->toBeFalse();
    expect($policy->viewResume($user))->toBeFalse();
});
