<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ResumeStatusHistoryModel extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'resume_status_history';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'resume_id',
        'from_status',
        'to_status',
        'changed_at',
    ];

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resume_id' => 'integer',
            'from_status' => 'string',
            'to_status' => 'string',
            'changed_at' => 'datetime',
        ];
    }
}
