<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

final class ResumeModel extends Model
{
    protected $table = 'resumes';

    protected $fillable = [
        'name',
        'email',
    ];
}
