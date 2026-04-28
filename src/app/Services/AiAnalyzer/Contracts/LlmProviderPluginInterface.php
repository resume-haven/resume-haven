<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer\Contracts;

use App\Dto\AnalyzeRequestDto;
use Laravel\Ai\Responses\StructuredAgentResponse;

interface LlmProviderPluginInterface
{
    public function buildPromptPayload(AnalyzeRequestDto $request): string;

    public function normalizeResponse(StructuredAgentResponse $response): string;

    public function mapProviderException(\Throwable $exception): \Throwable;
}

