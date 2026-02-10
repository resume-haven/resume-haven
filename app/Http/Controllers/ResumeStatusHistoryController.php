<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\ResumeQueryService;
use Illuminate\Http\JsonResponse;

final class ResumeStatusHistoryController extends Controller
{
    public function __construct(private ResumeQueryService $queries)
    {
    }

    public function __invoke(int $id): JsonResponse
    {
        $history = $this->queries->getStatusHistory($id);

        if ($history === null) {
            return response()->json(['message' => 'Resume not found.'], 404);
        }

        return response()->json($history);
    }
}
