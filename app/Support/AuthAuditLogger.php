<?php

declare(strict_types=1);

namespace App\Support;

use App\Infrastructure\Persistence\AuthAuditLogModel;
use App\Infrastructure\Persistence\UserModel;

final class AuthAuditLogger
{
    /**
     * @param array<string, mixed> $context
     */
    public static function log(string $event, ?UserModel $user = null, array $context = []): void
    {
        AuthAuditLogModel::query()->create([
            'user_id' => $user?->id,
            'event' => $event,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'context' => $context !== [] ? $context : null,
        ]);
    }
}
