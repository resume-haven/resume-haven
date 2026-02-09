<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Contracts\ResumeReadRepositoryInterface;
use App\Application\Contracts\UserReadRepositoryInterface;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentResumeReadRepository;
use App\Infrastructure\Repositories\EloquentResumeRepository;
use App\Infrastructure\Repositories\EloquentUserReadRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ResumeRepositoryInterface::class, EloquentResumeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(ResumeReadRepositoryInterface::class, EloquentResumeReadRepository::class);
        $this->app->bind(UserReadRepositoryInterface::class, EloquentUserReadRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
