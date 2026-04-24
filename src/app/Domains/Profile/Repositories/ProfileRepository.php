<?php

declare(strict_types=1);

namespace App\Domains\Profile\Repositories;

use App\Models\StoredResume;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ProfileRepository
{
    private const MIN_RETENTION_HOURS = 1;

    public function existsByToken(string $token): bool
    {
        return StoredResume::query()->where('token', $token)->exists();
    }

    public function store(string $token, string $encryptedCv, ?int $userId = null): void
    {
        StoredResume::query()->create([
            'token' => $token,
            'user_id' => $userId,
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

    /** @return Collection<int, StoredResume> */
    public function getByUser(int $userId): Collection
    {
        /** @var Collection<int, StoredResume> $resumes */
        $resumes = StoredResume::query()
            ->where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->get();

        return $resumes;
    }

    /** @return LengthAwarePaginator<int, StoredResume> */
    public function paginateByUser(int $userId, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, StoredResume> $paginator */
        $paginator = StoredResume::query()
            ->where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return $paginator;
    }

    public function claimByToken(string $token, int $userId): void
    {
        StoredResume::query()
            ->where('token', $token)
            ->whereNull('user_id')
            ->update(['user_id' => $userId]);
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
            ->whereNull('user_id')
            ->where(function ($query) use ($cutoff): void {
                $query->whereNotNull('last_accessed_at')
                    ->where('last_accessed_at', '<', $cutoff)
                    ->orWhere(function ($fallback) use ($cutoff): void {
                        $fallback->whereNull('last_accessed_at')
                            ->where('created_at', '<', $cutoff);
                    });
            })
            ->delete();

        return $deleted;
    }

    private function retentionHours(): int
    {
        $hoursConfig = config('profile.resume_retention_hours', 168);
        $hours = is_numeric($hoursConfig) ? (int) $hoursConfig : 168;

        return max($hours, self::MIN_RETENTION_HOURS);
    }
}
