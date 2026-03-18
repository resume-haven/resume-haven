<?php

declare(strict_types=1);

use App\Domains\Profile\Dto\AnalysisBaselineDto;
use App\Domains\Profile\Repositories\AnalysisBaselineRepository;
use App\Models\AnalysisBaseline;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('AnalysisBaselineRepository', function () {
    it('liefert null wenn keine Baseline existiert', function () {
        $repository = app(AnalysisBaselineRepository::class);

        $baseline = $repository->find('missing-key', 'missing-hash');

        expect($baseline)->toBeNull();
    });

    it('upsert aktualisiert bestehende Baselines statt Duplikate anzulegen', function () {
        $repository = app(AnalysisBaselineRepository::class);

        $repository->upsert(new AnalysisBaselineDto(
            baselineKey: 'baseline-key',
            jobHash: 'job-hash',
            scorePercentage: 40,
            matchCount: 2,
            gapCount: 5,
            recommendations: [
                ['gap' => 'Docker', 'priority' => 'high'],
            ],
        ));

        $repository->upsert(new AnalysisBaselineDto(
            baselineKey: 'baseline-key',
            jobHash: 'job-hash',
            scorePercentage: 75,
            matchCount: 6,
            gapCount: 1,
            recommendations: [
                ['gap' => 'Testing', 'priority' => 'medium'],
            ],
        ));

        $stored = AnalysisBaseline::query()->get();

        expect($stored)->toHaveCount(1)
            ->and($stored->first()?->score_percentage)->toBe(75)
            ->and($stored->first()?->match_count)->toBe(6)
            ->and($stored->first()?->gap_count)->toBe(1)
            ->and($stored->first()?->recommendations)->toBe([
                ['gap' => 'Testing', 'priority' => 'medium'],
            ]);
    });

    it('normalisiert Empfehlungen beim Laden und filtert ungueltige Eintraege', function () {
        $repository = app(AnalysisBaselineRepository::class);

        AnalysisBaseline::query()->create([
            'baseline_key' => 'baseline-key',
            'job_hash' => 'job-hash',
            'score_percentage' => 55,
            'match_count' => 3,
            'gap_count' => 2,
            'recommendations' => [
                ['gap' => 'Docker', 'priority' => 'high'],
                ['gap' => 'Kubernetes', 'priority' => 'urgent'],
                ['gap' => 123, 'priority' => 'medium'],
                ['priority' => 'low'],
                'invalid-row',
            ],
        ]);

        $baseline = $repository->find('baseline-key', 'job-hash');

        expect($baseline)->not()->toBeNull()
            ->and($baseline?->recommendations)->toBe([
                ['gap' => 'Docker', 'priority' => 'high'],
            ]);
    });

    it('liefert leere Empfehlungen wenn der Persistenzwert null ist', function () {
        $repository = app(AnalysisBaselineRepository::class);

        AnalysisBaseline::query()->create([
            'baseline_key' => 'baseline-null',
            'job_hash' => 'job-null',
            'score_percentage' => 30,
            'match_count' => 1,
            'gap_count' => 4,
            'recommendations' => null,
        ]);

        $baseline = $repository->find('baseline-null', 'job-null');

        expect($baseline)->not()->toBeNull()
            ->and($baseline?->recommendations)->toBe([]);
    });
});
