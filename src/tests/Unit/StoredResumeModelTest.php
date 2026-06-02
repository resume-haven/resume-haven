<?php

declare(strict_types=1);

use App\Models\StoredResume;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

describe('StoredResume model', function (): void {
    test('fillable attributes are set correctly', function (): void {
        $resume = new StoredResume();

        expect($resume->getFillable())->toBe([
            'token',
            'user_id',
            'file_name',
            'original_filename',
            'encrypted_cv',
            'last_accessed_at',
        ]);
    });

    test('casts include datetime and integer definitions', function (): void {
        $resume = new StoredResume();

        $casts = $resume->getCasts();

        expect($casts['last_accessed_at'])->toBe('datetime');
        expect($casts['user_id'])->toBe('integer');
    });

    test('user relation is a belongs-to relation to user model', function (): void {
        $resume = new StoredResume();

        $relation = $resume->user();

        expect($relation)->toBeInstanceOf(BelongsTo::class);
        expect($relation->getRelated())->toBeInstanceOf(User::class);
        expect($relation->getForeignKeyName())->toBe('user_id');
        expect($relation->getOwnerKeyName())->toBe('id');
    });
});
