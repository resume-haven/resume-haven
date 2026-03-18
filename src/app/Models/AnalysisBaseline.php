<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int                                                                  $id
 * @property string                                                               $baseline_key
 * @property string                                                               $job_hash
 * @property int                                                                  $score_percentage
 * @property int                                                                  $match_count
 * @property int                                                                  $gap_count
 * @property array<int, array{gap: string, priority: 'high'|'medium'|'low'}>|null $recommendations
 * @property \Illuminate\Support\Carbon                                           $created_at
 * @property \Illuminate\Support\Carbon                                           $updated_at
 */
class AnalysisBaseline extends Model
{
    protected $table = 'analysis_baselines';

    /** @var array<int, string> */
    protected $fillable = [
        'baseline_key',
        'job_hash',
        'score_percentage',
        'match_count',
        'gap_count',
        'recommendations',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'recommendations' => 'array',
    ];
}
