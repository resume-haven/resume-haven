<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\StoredResume;
use App\Models\User;
use App\Policies\ProfilePolicy;

it('allows owner to view and delete own resume', function (): void {
    $policy = new ProfilePolicy();

    $user = new User();
    $user->id = 10;
    $user->role = UserRole::User;

    $resume = new StoredResume();
    $resume->user_id = 10;

    expect($policy->view($user, $resume))->toBeTrue();
    expect($policy->delete($user, $resume))->toBeTrue();
});

it('denies regular user for foreign resume', function (): void {
    $policy = new ProfilePolicy();

    $user = new User();
    $user->id = 11;
    $user->role = UserRole::User;

    $resume = new StoredResume();
    $resume->user_id = 12;

    expect($policy->view($user, $resume))->toBeFalse();
    expect($policy->delete($user, $resume))->toBeFalse();
});

it('allows admin for foreign resume', function (): void {
    $policy = new ProfilePolicy();

    $admin = new User();
    $admin->id = 13;
    $admin->role = UserRole::Admin;

    $resume = new StoredResume();
    $resume->user_id = 99;

    expect($policy->view($admin, $resume))->toBeTrue();
    expect($policy->delete($admin, $resume))->toBeTrue();
});
