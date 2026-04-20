<?php

declare(strict_types=1);

/**
 * SOLID Architecture Tests
 *
 * Prüft SOLID-Prinzipien auf Namespace- und Klassenebene:
 *   - Single Action Controllers: alle Controller (außer Basis-Controller) haben __invoke
 *   - Interface-based Design: AI-Analyzer implementieren AiAnalyzerInterface
 *   - Immutable DTOs: Profile-DTOs sind readonly
 */
arch('Analyze Controller uses Single Action pattern')
    ->expect('App\Http\Controllers\AnalyzeController')
    ->toHaveMethod('__invoke');

arch('BuildCompetenceResumeController uses Single Action pattern')
    ->expect('App\Http\Controllers\BuildCompetenceResumeController')
    ->toHaveMethod('__invoke');

arch('LoadResumeController uses Single Action pattern')
    ->expect('App\Http\Controllers\LoadResumeController')
    ->toHaveMethod('__invoke');

arch('StoreResumeController uses Single Action pattern')
    ->expect('App\Http\Controllers\StoreResumeController')
    ->toHaveMethod('__invoke');

arch('UseCompetenceResumeController uses Single Action pattern')
    ->expect('App\Http\Controllers\UseCompetenceResumeController')
    ->toHaveMethod('__invoke');

arch('AI Analyzer implementations use AiAnalyzerInterface contract')
    ->expect('App\Services\AiAnalyzer\GeminiAiAnalyzer')
    ->toImplement('App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface');

arch('Mock AI Analyzer uses AiAnalyzerInterface contract')
    ->expect('App\Services\AiAnalyzer\MockAiAnalyzer')
    ->toImplement('App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface');

arch('Profile DTOs are immutable readonly classes')
    ->expect('App\Domains\Profile\Dto')
    ->toBeReadonly();

arch('Analysis readonly DTOs remain readonly')
    ->expect([
        'App\Domains\Analysis\Dto\RecommendationDto',
        'App\Domains\Analysis\Dto\ScoreDeltaDto',
        'App\Domains\Analysis\Dto\RecommendationDeltaDto',
        'App\Domains\Analysis\Dto\TagMatchDto',
    ])
    ->toBeReadonly();

arch('Controllers do not use Eloquent Models directly')
    ->expect('App\Http\Controllers')
    ->not->toUse('App\Models');

arch('Services depend on interfaces, not concrete AI implementations')
    ->expect('App\Services\AnalyzeApplicationService')
    ->not->toUse('App\Services\AiAnalyzer\GeminiAiAnalyzer')
    ->not->toUse('App\Services\AiAnalyzer\MockAiAnalyzer');
