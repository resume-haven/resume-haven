<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domains\Analysis\Commands\AnalyzeJobAndResumeCommand;
use App\Domains\Analysis\Handlers\AnalyzeJobAndResumeHandler;
use App\Domains\Analysis\UseCases\ExtractDataUseCase\ExtractDataUseCase;
use App\Domains\Analysis\UseCases\ExtractDataUseCase\ExtractRequirementsAction;
use App\Domains\Analysis\UseCases\ExtractDataUseCase\ExtractExperiencesAction;
use App\Domains\Analysis\UseCases\MatchingUseCase\MatchingUseCase;
use App\Domains\Analysis\UseCases\MatchingUseCase\MatchAction;
use App\Domains\Analysis\UseCases\GapAnalysisUseCase\GapAnalysisUseCase;
use App\Domains\Analysis\UseCases\GapAnalysisUseCase\FindGapsAction;
use App\Domains\Analysis\UseCases\ScoringUseCase\ScoringUseCase;
use App\Domains\Analysis\UseCases\ScoringUseCase\CalculateScoreAction;
use App\Domains\Analysis\Cache\Actions\GetCachedAnalysisAction;
use App\Domains\Analysis\Cache\Actions\StoreCachedAnalysisAction;
use App\Domains\Analysis\Cache\Repositories\AnalysisCacheRepository;

/**
 * ServiceProvider: Analysis Domain
 * Registriert alle Dependencies für die Analysis Domain
 */
class AnalysisDomainServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Actions
        $this->app->singleton(ExtractRequirementsAction::class);
        $this->app->singleton(ExtractExperiencesAction::class);
        $this->app->singleton(MatchAction::class);
        $this->app->singleton(FindGapsAction::class);
        $this->app->singleton(CalculateScoreAction::class);

        // UseCases
        // TODO: ExtractDataUseCase wird später wieder verwendet, wenn Extraction aus der AI herausgenommen wird
        // $this->app->singleton(ExtractDataUseCase::class);
        $this->app->singleton(MatchingUseCase::class);
        $this->app->singleton(GapAnalysisUseCase::class);
        $this->app->singleton(ScoringUseCase::class);

        // Cache
        $this->app->singleton(AnalysisCacheRepository::class);
        $this->app->singleton(GetCachedAnalysisAction::class);
        $this->app->singleton(StoreCachedAnalysisAction::class);

        // Command Handler
        $this->app->singleton(AnalyzeJobAndResumeHandler::class);
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Command-Handler mapping via handle() method in Command
        // Laravel Bus automatically calls AnalyzeJobAndResumeCommand::handle()
    }
}
