<?php

declare(strict_types=1);

use App\Domains\Analysis\Dto\ScoreResultDto;
use App\Domains\Analysis\UseCases\PresentationUseCase\BuildAnalysisComparisonAction;
use App\Models\AnalysisBaseline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

uses(RefreshDatabase::class);

function makeScore(int $percentage): ScoreResultDto
{
    return new ScoreResultDto(
        percentage: $percentage,
        rating: 'Test',
        bgColor: 'bg-blue-50',
        textColor: 'text-blue-900',
        barColor: 'bg-blue-500',
        matchCount: 0,
        gapCount: 0,
    );
}

function makeRequestWithSession(array $sessionData = []): Request
{
    $request = Request::create('/analyze', 'POST');

    /** @var Store $session */
    $session = app('session')->driver('array');
    $session->start();

    foreach ($sessionData as $key => $value) {
        $session->put($key, $value);
    }

    $request->setLaravelSession($session);

    return $request;
}

describe('BuildAnalysisComparisonAction', function () {
    it('gibt null zurück wenn kein Score vorliegt', function () {
        $action = app(BuildAnalysisComparisonAction::class);
        $request = makeRequestWithSession([
            'cv_source' => 'competence_resume',
            'resume_token' => 'token-null-score',
        ]);

        $comparison = $action->execute(
            request: $request,
            jobText: 'Laravel Backend Stelle',
            score: null,
            matchCount: 2,
            gapCount: 1,
            currentRecommendations: [],
        );

        expect($comparison)->toBeNull();
    });

    it('speichert bei normaler Analyse eine Baseline und liefert keinen Vergleich', function () {
        $action = app(BuildAnalysisComparisonAction::class);
        $jobText = "Laravel\n  Backend    Developer";
        $request = makeRequestWithSession([
            'cv_source' => 'uploaded_resume',
            'resume_token' => 'token-store',
        ]);

        $comparison = $action->execute(
            request: $request,
            jobText: $jobText,
            score: makeScore(62),
            matchCount: 5,
            gapCount: 3,
            currentRecommendations: [
                ['gap' => 'Docker', 'priority' => 'high'],
            ],
        );

        $jobHash = hash('sha256', 'Laravel Backend Developer');

        expect($comparison)->toBeNull();
        expect(AnalysisBaseline::query()->where('baseline_key', 'token:token-store')->where('job_hash', $jobHash)->exists())->toBeTrue();
    });

    it('berechnet Delta-Werte für competence_resume auf Basis persistenter Baseline', function () {
        $action = app(BuildAnalysisComparisonAction::class);
        $jobText = 'Senior Laravel Engineer';
        $jobHash = hash('sha256', $jobText);

        AnalysisBaseline::query()->create([
            'baseline_key' => 'token:token-compare',
            'job_hash' => $jobHash,
            'score_percentage' => 50,
            'match_count' => 4,
            'gap_count' => 3,
            'recommendations' => [
                ['gap' => 'Docker', 'priority' => 'high'],
            ],
        ]);

        $request = makeRequestWithSession([
            'cv_source' => 'competence_resume',
            'resume_token' => 'token-compare',
        ]);

        $comparison = $action->execute(
            request: $request,
            jobText: $jobText,
            score: makeScore(70),
            matchCount: 6,
            gapCount: 1,
            currentRecommendations: [
                ['gap' => 'Docker', 'priority' => 'medium'],
                ['gap' => 'API Design', 'priority' => 'low'],
            ],
        );

        expect($comparison)->not()->toBeNull();

        $data = $comparison?->toArray() ?? [];
        $scoreDelta = is_array($data['score_delta'] ?? null) ? $data['score_delta'] : [];
        $recommendationDeltas = is_array($data['recommendation_deltas'] ?? null) ? $data['recommendation_deltas'] : [];

        expect($data['has_comparison'] ?? false)->toBeTrue()
            ->and($scoreDelta['delta'] ?? null)->toBe(20)
            ->and($scoreDelta['arrow'] ?? null)->toBe('↑')
            ->and($data['match_delta'] ?? null)->toBe(2)
            ->and($data['gap_delta'] ?? null)->toBe(-2)
            ->and(count($recommendationDeltas))->toBe(1)
            ->and($recommendationDeltas[0]['gap'] ?? null)->toBe('Docker')
            ->and($recommendationDeltas[0]['direction'] ?? null)->toBe('improved');
    });

    it('markiert Gleichstand mit neutralem Impact', function () {
        $action = app(BuildAnalysisComparisonAction::class);
        $jobText = 'Laravel Fullstack Engineer';
        $jobHash = hash('sha256', $jobText);

        AnalysisBaseline::query()->create([
            'baseline_key' => 'token:token-same',
            'job_hash' => $jobHash,
            'score_percentage' => 60,
            'match_count' => 3,
            'gap_count' => 2,
            'recommendations' => [
                ['gap' => 'Docker', 'priority' => 'medium'],
            ],
        ]);

        $request = makeRequestWithSession([
            'cv_source' => 'competence_resume',
            'resume_token' => 'token-same',
        ]);

        $comparison = $action->execute(
            request: $request,
            jobText: $jobText,
            score: makeScore(60),
            matchCount: 3,
            gapCount: 2,
            currentRecommendations: [
                ['gap' => 'Docker', 'priority' => 'medium'],
            ],
        );

        expect($comparison)->not()->toBeNull();

        $data = $comparison?->toArray() ?? [];
        $scoreDelta = is_array($data['score_delta'] ?? null) ? $data['score_delta'] : [];
        $recommendationDeltas = is_array($data['recommendation_deltas'] ?? null) ? $data['recommendation_deltas'] : [];

        expect($scoreDelta['delta'] ?? null)->toBe(0)
            ->and($scoreDelta['direction'] ?? null)->toBe('same')
            ->and($scoreDelta['arrow'] ?? null)->toBe('→')
            ->and($data['match_delta'] ?? null)->toBe(0)
            ->and($data['gap_delta'] ?? null)->toBe(0)
            ->and(count($recommendationDeltas))->toBe(1)
            ->and($recommendationDeltas[0]['direction'] ?? null)->toBe('same')
            ->and($recommendationDeltas[0]['arrow'] ?? null)->toBe('→');
    });

    it('markiert Verschlechterungen mit negativem Impact', function () {
        $action = app(BuildAnalysisComparisonAction::class);
        $jobText = 'Senior Backend Engineer';
        $jobHash = hash('sha256', $jobText);

        AnalysisBaseline::query()->create([
            'baseline_key' => 'token:token-worse',
            'job_hash' => $jobHash,
            'score_percentage' => 80,
            'match_count' => 8,
            'gap_count' => 1,
            'recommendations' => [
                ['gap' => 'Docker', 'priority' => 'low'],
            ],
        ]);

        $request = makeRequestWithSession([
            'cv_source' => 'competence_resume',
            'resume_token' => 'token-worse',
        ]);

        $comparison = $action->execute(
            request: $request,
            jobText: $jobText,
            score: makeScore(50),
            matchCount: 4,
            gapCount: 5,
            currentRecommendations: [
                ['gap' => 'Docker', 'priority' => 'high'],
            ],
        );

        expect($comparison)->not()->toBeNull();

        $data = $comparison?->toArray() ?? [];
        $scoreDelta = is_array($data['score_delta'] ?? null) ? $data['score_delta'] : [];
        $recommendationDeltas = is_array($data['recommendation_deltas'] ?? null) ? $data['recommendation_deltas'] : [];

        expect($scoreDelta['delta'] ?? null)->toBe(-30)
            ->and($scoreDelta['direction'] ?? null)->toBe('worse')
            ->and($scoreDelta['arrow'] ?? null)->toBe('↓')
            ->and($data['match_delta'] ?? null)->toBe(-4)
            ->and($data['gap_delta'] ?? null)->toBe(4)
            ->and(count($recommendationDeltas))->toBe(1)
            ->and($recommendationDeltas[0]['direction'] ?? null)->toBe('worse')
            ->and($recommendationDeltas[0]['arrow'] ?? null)->toBe('↓');
    });

    it('nutzt Session-Fallback wenn keine persistente Baseline gefunden wird', function () {
        $action = app(BuildAnalysisComparisonAction::class);
        $request = makeRequestWithSession([
            'cv_source' => 'competence_resume',
            'resume_token' => 'token-fallback',
            'analysis_baseline_snapshot' => [
                'score_percentage' => 40,
                'match_count' => 2,
                'gap_count' => 5,
                'recommendations' => [
                    ['gap' => 'Kubernetes', 'priority' => 'high'],
                    ['gap' => 'Invalid', 'priority' => 'urgent'],
                    'invalid-row',
                ],
            ],
        ]);

        $comparison = $action->execute(
            request: $request,
            jobText: 'Andere Stelle ohne persistente Baseline',
            score: makeScore(55),
            matchCount: 4,
            gapCount: 2,
            currentRecommendations: [
                ['gap' => 'Kubernetes', 'priority' => 'low'],
            ],
        );

        expect($comparison)->not()->toBeNull();

        $data = $comparison?->toArray() ?? [];
        $scoreDelta = is_array($data['score_delta'] ?? null) ? $data['score_delta'] : [];
        $recommendationDeltas = is_array($data['recommendation_deltas'] ?? null) ? $data['recommendation_deltas'] : [];

        expect($data['message'] ?? null)->toBe('Vergleich aus Session-Fallback (keine persistente Baseline gefunden).')
            ->and($scoreDelta['delta'] ?? null)->toBe(15)
            ->and($data['match_delta'] ?? null)->toBe(2)
            ->and($data['gap_delta'] ?? null)->toBe(-3)
            ->and(count($recommendationDeltas))->toBe(1)
            ->and($recommendationDeltas[0]['gap'] ?? null)->toBe('Kubernetes')
            ->and($recommendationDeltas[0]['arrow'] ?? null)->toBe('↑');
    });

    it('liefert null bei ungültigem Session-Fallback-Snapshot', function () {
        $action = app(BuildAnalysisComparisonAction::class);
        $request = makeRequestWithSession([
            'cv_source' => 'competence_resume',
            'resume_token' => 'token-invalid-fallback',
            'analysis_baseline_snapshot' => [
                'score_percentage' => '40',
                'match_count' => 2,
                'gap_count' => 1,
                'recommendations' => [],
            ],
        ]);

        $comparison = $action->execute(
            request: $request,
            jobText: 'Keine DB Baseline vorhanden',
            score: makeScore(45),
            matchCount: 3,
            gapCount: 1,
            currentRecommendations: [],
        );

        expect($comparison)->toBeNull();
    });
});
