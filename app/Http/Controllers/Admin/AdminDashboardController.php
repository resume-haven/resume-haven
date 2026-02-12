<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Services\ResumeQueryService;
use App\Application\Services\UserQueryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AdminDashboardController extends Controller
{
    public function __construct(
        private ResumeQueryService $resumes,
        private UserQueryService $users,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $this->authorize('admin.dashboard');

        $recentResumes = $this->resumes->list(5, 0);
        $recentUsers = $this->users->list(5, 0);

        return response()->view('admin.dashboard', [
            'recentResumes' => $recentResumes,
            'recentUsers' => $recentUsers,
            'totals' => [
                'resumes' => $this->resumes->getTotal(),
                'users' => $this->users->getTotal(),
                'status_events' => $this->resumes->getStatusHistoryTotal(),
            ],
        ]);
    }
}
