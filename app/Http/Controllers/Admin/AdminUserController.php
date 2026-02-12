<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Services\UserQueryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AdminUserController extends Controller
{
    public function __construct(private UserQueryService $users)
    {
    }

    public function index(Request $request): Response
    {
        $this->authorize('admin.users.view');

        $page = max(1, (int) $request->query('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $total = $this->users->getTotal();
        $lastPage = max(1, (int) ceil($total / $limit));

        return response()->view('admin.users.index', [
            'users' => $this->users->list($limit, $offset),
            'total' => $total,
            'page' => $page,
            'lastPage' => $lastPage,
        ]);
    }
}
