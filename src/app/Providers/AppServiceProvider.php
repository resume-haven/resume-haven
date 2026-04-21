<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\AutoClaimResumesListener;
use App\Models\StoredResume;
use App\Policies\ProfilePolicy;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\GeminiAiAnalyzer;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // AI Provider Strategy Pattern Binding
        $this->app->bind(AiAnalyzerInterface::class, function ($app) {
            $provider = config('ai.provider') ?? 'mock'; // null -> default to 'mock'

            if (! is_string($provider)) {
                throw new \InvalidArgumentException('AI provider configuration must be a string.');
            }

            return match ($provider) {
                'gemini' => $app->make(GeminiAiAnalyzer::class),
                'mock' => $app->make(MockAiAnalyzer::class),
                default => throw new \InvalidArgumentException('Unknown AI provider: '.$provider.'. Available: mock, gemini'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, AutoClaimResumesListener::class);
        Gate::policy(StoredResume::class, ProfilePolicy::class);
    }
}
