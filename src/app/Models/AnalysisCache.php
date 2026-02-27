<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisCache extends Model
{
    protected $table = 'analysis_cache';
    protected $fillable = [
        'request_hash',
        'job_text',
        'cv_text',
        'result',
    ];
    protected $casts = [
        'result' => 'array',
    ];
}
