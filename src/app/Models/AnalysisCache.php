<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int                                                                                                                                                                                          $id
 * @property string                                                                                                                                                                                       $request_hash
 * @property string                                                                                                                                                                                       $job_text
 * @property string                                                                                                                                                                                       $cv_text
 * @property array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, error?: string|null} $result
 * @property \Illuminate\Support\Carbon                                                                                                                                                                   $created_at
 * @property \Illuminate\Support\Carbon                                                                                                                                                                   $updated_at
 */
class AnalysisCache extends Model
{
    protected $table = 'analysis_cache';

    /** @var array<int, string> */
    protected $fillable = [
        'request_hash',
        'job_text',
        'cv_text',
        'result',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'result' => 'array',
    ];
}
