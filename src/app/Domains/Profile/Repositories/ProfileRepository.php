<?php

declare(strict_types=1);

namespace App\Domains\Profile\Repositories;

use App\Models\StoredResume;
use Illuminate\Support\Carbon;

class ProfileRepository
{
    private const MIN_RETENTION_HOURS = 1;

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

    public function deleteByToken(string $token): void
    {
        StoredResume::query()
            ->where('token', $token)
            ->delete();
    }

    public function isExpired(StoredResume $resume): bool
    {
        $cutoff = Carbon::now()->subHours($this->retentionHours());
        $reference = $resume->last_accessed_at ?? $resume->created_at;

        return $reference->lt($cutoff);
    }

    public function pruneExpired(): int
    {
        $cutoff = Carbon::now()->subHours($this->retentionHours());

        $deleted = StoredResume::query()
            ->where(function ($query) use ($cutoff): void {
                $query->whereNotNull('last_accessed_at')
                    ->where('last_accessed_at', '<', $cutoff)
                    ->orWhere(function ($fallback) use ($cutoff): void {
                        $fallback->whereNull('last_accessed_at')
                            ->where('created_at', '<', $cutoff);
                    });
            })
            ->delete();

        if (! is_numeric($deleted)) {
            return 0;
        }

        return (int) $deleted;
    }

    private function retentionHours(): int
    {
        $hoursConfig = config('profile.resume_retention_hours', 168);
        $hours = is_numeric($hoursConfig) ? (int) $hoursConfig : 168;

        return max($hours, self::MIN_RETENTION_HOURS);
    }
}
