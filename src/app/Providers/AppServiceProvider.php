<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\AutoClaimResumesListener;
use App\Models\StoredResume;
use App\Policies\ProfilePolicy;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
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
        // AI Provider Strategy Pattern Binding – driven by config('ai.analyzers')
        $this->app->bind(AiAnalyzerInterface::class, function ($app) {
            $provider = config('ai.provider') ?? 'mock'; // null -> default to 'mock'

            if (! is_string($provider)) {
                throw new \InvalidArgumentException('AI provider configuration must be a string.');
            }

            $analyzers = config('ai.analyzers', []);

            if (! is_array($analyzers)) {
                throw new \InvalidArgumentException('AI analyzers configuration must be an array.');
            }

            if (! array_key_exists($provider, $analyzers)) {
                $available = implode(', ', array_keys($analyzers));
                throw new \InvalidArgumentException('Unknown AI provider: '.$provider.'. Available: '.$available);
            }

            $analyzerClass = $analyzers[$provider];

            if (! is_string($analyzerClass)) {
                throw new \InvalidArgumentException('AI analyzer class must be a string.');
            }

            if (! is_a($analyzerClass, AiAnalyzerInterface::class, true)) {
                throw new \InvalidArgumentException('AI analyzer class must implement '.AiAnalyzerInterface::class.'.');
            }

            return $app->make($analyzerClass);
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
