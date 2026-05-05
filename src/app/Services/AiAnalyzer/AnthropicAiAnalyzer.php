<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Ai\Agents\Analyzer;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;

/**
 * Anthropic AI Analyzer - Proof of Concept Implementation
 *
 * Integriert Anthropic Claude als zweiter LLM-Provider
 * via Laravel AI Package.
 * Implementiert provider-spezifisches Exception-Mapping für Anthropic-Fehler.
 */
class AnthropicAiAnalyzer extends AbstractLlmAiAnalyzer
{
    public function __construct(
        ValidateAiResponseAction $validateResponse,
        ParseAiResponseAction $parseResponse,
    ) {
        parent::__construct($validateResponse, $parseResponse);
    }

    public function isAvailable(): bool
    {
        return ! empty(config('ai.providers.anthropic.key'));
    }

    public function getProviderName(): string
    {
        return 'anthropic';
    }

    protected function createAnalyzer(): Analyzer
    {
        return new Analyzer();
    }

    /**
     * Mappt Anthropic-spezifische Exceptions auf benutzerfreundliche Meldungen
     * und strukturierte Runtime-Exceptions.
     */
    public function mapProviderException(\Throwable $exception): \Throwable
    {
        $message = strtolower($exception->getMessage());

        // Anthropic Token Limit: "insufficient_tokens" oder "context_length" → ZUERST prüfen
        if (str_contains($message, 'insufficient_tokens') || str_contains($message, 'context_length')) {
            return new \RuntimeException('Eingabe oder Kontext zu lang für Anthropic API.', 0, $exception);
        }

        // Anthropic Rate Limit: 429 oder explizite "rate_limit_error"
        if (str_contains($message, 'rate_limit') || str_contains($message, '429')) {
            return new \RuntimeException('Anthropic API rate limit erreicht. Bitte später erneut versuchen.', 0, $exception);
        }

        // Anthropic Overloaded: "overloaded_error"
        if (str_contains($message, 'overloaded')) {
            return new \RuntimeException('Anthropic API ist momentan überlastet. Bitte später versuchen.', 0, $exception);
        }

        // Anthropic Authentication: "authentication_error"
        if (str_contains($message, 'authentication') || str_contains($message, 'unauthorized')) {
            return new \RuntimeException('Authentifizierung bei Anthropic fehlgeschlagen.', 0, $exception);
        }

        // Anthropic Invalid Request: "invalid_request_error"
        if (str_contains($message, 'invalid_request')) {
            return new \RuntimeException('Ungültige Anfrage an Anthropic API.', 0, $exception);
        }

        // Standard-Fallback auf generisches Exception-Mapping
        return $exception;
    }

    protected function classifyProviderTransientException(\Throwable $exception): ?string
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'rate_limit') || str_contains($message, '429')) {
            return 'anthropic:rate_limit';
        }

        if (str_contains($message, 'overloaded')) {
            return 'anthropic:overloaded';
        }

        return null;
    }
}
