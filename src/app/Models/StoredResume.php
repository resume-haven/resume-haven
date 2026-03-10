<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $token
 * @property string                          $encrypted_cv
 * @property \Illuminate\Support\Carbon|null $last_accessed_at
 * @property \Illuminate\Support\Carbon      $created_at
 * @property \Illuminate\Support\Carbon      $updated_at
 */
class StoredResume extends Model
{
    protected $table = 'stored_resumes';

    /** @var array<int, string> */
    protected $fillable = [
        'token',
        'encrypted_cv',
        'last_accessed_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'last_accessed_at' => 'datetime',
    ];
}
