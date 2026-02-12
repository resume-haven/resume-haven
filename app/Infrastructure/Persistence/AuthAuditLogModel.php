<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AuthAuditLogModel extends Model
{
    use HasFactory;

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
}
