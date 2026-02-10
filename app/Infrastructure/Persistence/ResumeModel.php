<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ResumeModel extends Model
{
    use HasFactory;
    /**
     * @var string
     */
    protected $table = 'resumes';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email' => 'string',
            'status' => 'string',
        ];
    }
}
