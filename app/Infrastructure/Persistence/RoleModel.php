<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class RoleModel extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'roles';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];
}
