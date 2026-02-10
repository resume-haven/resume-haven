<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\ResumeCommandService;
use App\Application\Services\ResumeQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ResumeController extends Controller
{
    public function __construct(
        private ResumeQueryService $queries,
        private ResumeCommandService $commands,
    ) {
    }

    public function show(int $id): JsonResponse
    {
        $resume = $this->queries->getById($id);

        if ($resume === null) {
            return response()->json(['message' => 'Resume not found.'], 404);
        }

        return response()->json($resume);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $resume = $this->commands->create($data['name'], $data['email']);

        return response()->json([
            'id' => $resume->id->value,
            'name' => $resume->name->value,
            'email' => $resume->email->value,
        ], 201);
    }
}
