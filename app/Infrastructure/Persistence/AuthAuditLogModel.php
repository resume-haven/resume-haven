<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

final class AuthAuditLogModel extends Model
{
    use HasFactory;
    use Prunable;

    /**
     * @var string
     */
    protected $table = 'auth_audit_logs';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'event',
        'ip_address',
        'user_agent',
        'context',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'context' => 'array',
        ];
    }

    public function prunable(): \Illuminate\Database\Eloquent\Builder
    {
        return static::query()->where('created_at', '<', now()->subDays(30));
    }
}
