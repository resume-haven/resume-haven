<?php

declare(strict_types=1);

namespace App\Domains\Profile\Repositories;

use App\Models\StoredResume;
use Illuminate\Support\Carbon;

class ProfileRepository
{
    public function existsByToken(string $token): bool
    {
        return StoredResume::query()->where('token', $token)->exists();
    }

    public function store(string $token, string $encryptedCv): void
    {
        StoredResume::query()->create([
            'token' => $token,
            'encrypted_cv' => $encryptedCv,
            'last_accessed_at' => null,
        ]);
    }

    public function getByToken(string $token): ?StoredResume
    {
        /** @var StoredResume|null $resume */
        $resume = StoredResume::query()->where('token', $token)->first();

        return $resume;
    }

    public function touchLastAccessedByToken(string $token): void
    {
        StoredResume::query()
            ->where('token', $token)
            ->update(['last_accessed_at' => Carbon::now()]);
    }
}
