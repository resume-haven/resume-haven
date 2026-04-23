<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

describe('User model', function (): void {
    test('fillable und hidden sind korrekt gesetzt', function (): void {
        $user = new User();

        expect($user->getFillable())->toBe(['name', 'email', 'password', 'role']);
        expect($user->getHidden())->toBe(['password', 'remember_token']);
    });

    test('casts enthalten datetime, hashed und role enum', function (): void {
        $user = new class () extends User {
            public function exposeCasts(): array
            {
                return $this->casts();
            }
        };

        $casts = $user->exposeCasts();

        expect($casts['email_verified_at'])->toBe('datetime');
        expect($casts['password'])->toBe('hashed');
        expect($casts['role'])->toBe(UserRole::class);
    });

    test('role helper methods liefern erwartete werte', function (): void {
        $admin = new User();
        $admin->role = UserRole::Admin;

        $regular = new User();
        $regular->role = UserRole::User;

        expect($admin->isAdmin())->toBeTrue();
        expect($admin->isUser())->toBeFalse();
        expect($regular->isAdmin())->toBeFalse();
        expect($regular->isUser())->toBeTrue();
    });
});
