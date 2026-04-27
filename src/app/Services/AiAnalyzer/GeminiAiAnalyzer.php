<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Ai\Agents\Analyzer;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;

/**
 * Gemini AI Analyzer - Production Implementation
 *
 * Verwendet Laravel AI Package mit Gemini
 * Delegiert Response-Validierung und Parsing an dedizierte Actions.
 */
class GeminiAiAnalyzer extends AbstractLlmAiAnalyzer
{
    public function __construct(
        ValidateAiResponseAction $validateResponse,
        ParseAiResponseAction $parseResponse,
    ) {
        parent::__construct($validateResponse, $parseResponse);
    }

    public function isAvailable(): bool
    {
        return ! empty(config('ai.providers.gemini.key'));
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }

    protected function createAnalyzer(): Analyzer
    {
        return new Analyzer();
    }
}
