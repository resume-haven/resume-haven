<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Contracts\ResumeReadRepositoryInterface;
use App\Application\Contracts\ResumeStatusHistoryReadRepositoryInterface;
use App\Application\Contracts\UserReadRepositoryInterface;
use App\Domain\Contracts\ResumeStatusHistoryRepositoryInterface;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Persistence\ResumeStatusHistoryRepository;
use App\Infrastructure\Repositories\EloquentResumeStatusHistoryReadRepository;
use App\Infrastructure\Repositories\EloquentResumeReadRepository;
use App\Infrastructure\Repositories\EloquentResumeRepository;
use App\Infrastructure\Repositories\EloquentUserReadRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Infrastructure\Persistence\ResumeModel;
use App\Infrastructure\Persistence\UserModel;
use App\Policies\AdminPolicy;
use App\Policies\ResumePolicy;
use App\Policies\UserPolicy;
use App\Support\AuthAuditLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        $this->app->bind(ResumeStatusHistoryRepositoryInterface::class, ResumeStatusHistoryRepository::class);
        $this->app->bind(ResumeStatusHistoryReadRepositoryInterface::class, EloquentResumeStatusHistoryReadRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            AuthAuditLogger::log('auth.login', $event->user);
        });

        Event::listen(Logout::class, function (Logout $event): void {
            $user = $event->user instanceof UserModel ? $event->user : null;
            AuthAuditLogger::log('auth.logout', $user);
        });

        Event::listen(Verified::class, function (Verified $event): void {
            $user = $event->user instanceof UserModel ? $event->user : null;
            AuthAuditLogger::log('auth.verified', $user);
        });

        Gate::define('admin.dashboard', [AdminPolicy::class, 'dashboard']);
        Gate::define('admin.users.view', [AdminPolicy::class, 'viewUsers']);
        Gate::define('admin.resumes.view', [AdminPolicy::class, 'viewResumes']);
        Gate::define('admin.resumes.view-one', [AdminPolicy::class, 'viewResume']);
        Gate::define('admin.resumes.update', [AdminPolicy::class, 'updateResume']);
        Gate::define('admin.resumes.delete', [AdminPolicy::class, 'deleteResume']);
        Gate::policy(ResumeModel::class, ResumePolicy::class);
        Gate::policy(UserModel::class, UserPolicy::class);
    }
}
