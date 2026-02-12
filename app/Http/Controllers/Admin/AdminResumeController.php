<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Services\ResumeQueryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AdminResumeController extends Controller
{
    public function __construct(private ResumeQueryService $resumes)
    {
    }

    public function index(Request $request): Response
    {
        $this->authorize('admin.resumes.view');

        $page = max(1, (int) $request->query('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $total = $this->resumes->getTotal();
        $lastPage = max(1, (int) ceil($total / $limit));

        return response()->view('admin.resumes.index', [
            'resumes' => $this->resumes->list($limit, $offset),
            'total' => $total,
            'page' => $page,
            'lastPage' => $lastPage,
        ]);
    }

    public function show(int $id): Response
    {
        $this->authorize('admin.resumes.view-one');

        $resume = $this->resumes->getById($id);

        if ($resume === null) {
            abort(404);
        }

        return response()->view('admin.resumes.show', [
            'resume' => $resume,
            'history' => $this->resumes->getStatusHistory($id) ?? [],
        ]);
    }
}
