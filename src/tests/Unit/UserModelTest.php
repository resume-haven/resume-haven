<?php

declare(strict_types=1);

use App\Models\User;

describe('User model', function () {
    test('fillable und hidden sind korrekt gesetzt', function () {
        $user = new User();

        expect($user->getFillable())->toBe(['name', 'email', 'password']);
        expect($user->getHidden())->toBe(['password', 'remember_token']);
    });

    test('casts enthalten datetime und hashed', function () {
        $user = new class extends User {
            public function exposeCasts(): array
            {
                return $this->casts();
            }
        };

        $casts = $user->exposeCasts();

        expect($casts['email_verified_at'])->toBe('datetime');
        expect($casts['password'])->toBe('hashed');
    });
});

