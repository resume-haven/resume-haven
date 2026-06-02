<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $token
 * @property int|null    $user_id
 * @property string      $encrypted_cv
 * @property string|null $file_name
 * @property string|null $original_filename
 * @property Carbon|null $last_accessed_at
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 */
class StoredResume extends Model
{
    protected $table = 'stored_resumes';

    /** @var array<int, string> */
    protected $fillable = [
        'token',
        'user_id',
        'file_name',
        'original_filename',
        'encrypted_cv',
        'last_accessed_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'last_accessed_at' => 'datetime',
        'user_id' => 'integer',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
