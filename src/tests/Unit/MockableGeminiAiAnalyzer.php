<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Ai\Agents\Analyzer;
use App\Services\AiAnalyzer\GeminiAiAnalyzer;

/**
 * Testable subclass: erlaubt das Injizieren eines gemockten Analyzers
 * für den callAi()-Pfad ohne echte API-Calls.
 */
class MockableGeminiAiAnalyzer extends GeminiAiAnalyzer
{
    public ?Analyzer $injectedAnalyzer = null;

    protected function createAnalyzer(): Analyzer
    {
        if ($this->injectedAnalyzer !== null) {
            return $this->injectedAnalyzer;
        }

        return parent::createAnalyzer();
    }
}
