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

    public function mapProviderException(\Throwable $exception): \Throwable
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'quota') || str_contains($message, 'rate limit')) {
            return new \RuntimeException('Gemini API rate limit erreicht', 0, $exception);
        }

        return $exception;
    }

    protected function classifyProviderTransientException(\Throwable $exception): ?string
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'quota') || str_contains($message, 'rate limit')) {
            return 'gemini:rate_limit';
        }

        return null;
    }
}
