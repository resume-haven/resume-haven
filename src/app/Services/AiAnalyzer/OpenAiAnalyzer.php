<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Ai\Agents\Analyzer;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;

class OpenAiAnalyzer extends AbstractLlmAiAnalyzer
{
    public function __construct(
        ValidateAiResponseAction $validateResponse,
        ParseAiResponseAction $parseResponse,
    ) {
        parent::__construct($validateResponse, $parseResponse);
    }

    public function isAvailable(): bool
    {
        return ! empty(config('ai.providers.openai.key'));
    }

    public function getProviderName(): string
    {
        return 'openai';
    }

    protected function createAnalyzer(): Analyzer
    {
        return new Analyzer();
    }

    public function mapProviderException(\Throwable $exception): \Throwable
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'rate limit') || str_contains($message, '429')) {
            return new \RuntimeException('OpenAI API rate limit erreicht', 0, $exception);
        }

        return $exception;
    }
}
