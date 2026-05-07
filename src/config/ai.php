<?php

declare(strict_types=1);
use App\Services\AiAnalyzer\AnthropicAiAnalyzer;
use App\Services\AiAnalyzer\GeminiAiAnalyzer;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use App\Services\AiAnalyzer\OpenAiAnalyzer;

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Strategy
    |--------------------------------------------------------------------------
    |
    | This option controls which AI provider implementation to use.
    | Available options: 'mock', 'gemini', 'openai', 'anthropic'
    |
    | - mock: Development mode, no API calls, predefined test data
    | - gemini: Production mode, real AI requests via Gemini
    | - openai: Alternative provider, OpenAI GPT models
    | - anthropic: Alternative provider, Anthropic Claude models
    |
    */

    'provider' => env('AI_PROVIDER', 'mock'),

    /*
    |--------------------------------------------------------------------------
    | Analyzer Registry
    |--------------------------------------------------------------------------
    |
    | Maps each provider key to its concrete AiAnalyzerInterface implementation.
    | Adding a new LLM provider only requires a new entry here + the class itself –
    | no changes to AppServiceProvider are needed.
    |
    | Order matters: the first key appears first in error messages.
    |
    */

    'analyzers' => [
        'mock'       => MockAiAnalyzer::class,
        'gemini'     => GeminiAiAnalyzer::class,
        'openai'     => OpenAiAnalyzer::class,
        'anthropic'  => AnthropicAiAnalyzer::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Mock Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for MockAiAnalyzer during development.
    |
    | Scenarios:
    | - realistic: Balanced result (~60% score)
    | - high_score: Very good match (~90% score)
    | - low_score: Low match (~25% score)
    | - no_match: No matches (0% score)
    |
    */

    'mock' => [
        'scenario' => env('AI_MOCK_SCENARIO', 'realistic'),
        'delay_ms' => env('AI_MOCK_DELAY', 500), // Simulate API delay
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Minimaler Retry-PoC für transiente AI-Fehler. Das Verhalten kann über
    | Config vollständig deaktiviert werden, um auf Single-Attempt zurückzufallen.
    |
    */

    'retry' => [
        'enabled' => env('AI_RETRY_ENABLED', true),
        'max_attempts' => env('AI_RETRY_MAX_ATTEMPTS', 2),
        'backoff_ms' => env('AI_RETRY_BACKOFF_MS', 150),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider Names
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the AI providers below should be the
    | default for AI operations when no explicit provider is provided
    | for the operation. This should be any provider defined below.
    |
    */

    // 'default' => 'openai',
    'default' => 'gemini',
    'default_for_images' => 'gemini',
    'default_for_audio' => 'openai',
    'default_for_transcription' => 'openai',
    'default_for_embeddings' => 'openai',
    'default_for_reranking' => 'cohere',

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Below you may configure caching strategies for AI related operations
    | such as embedding generation. You are free to adjust these values
    | based on your application's available caching stores and needs.
    |
    */

    'caching' => [
        'embeddings' => [
            'cache' => false,
            'store' => env('CACHE_STORE', 'database'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    |
    | Below are each of your AI providers defined for this application. Each
    | represents an AI provider and API key combination which can be used
    | to perform tasks like text, image, and audio creation via agents.
    |
    */

    'providers' => [
        'anthropic' => [
            'driver' => 'anthropic',
            'key' => env('ANTHROPIC_API_KEY'),
        ],

        'azure' => [
            'driver' => 'azure',
            'key' => env('AZURE_OPENAI_API_KEY'),
            'url' => env('AZURE_OPENAI_URL'),
            'api_version' => env('AZURE_OPENAI_API_VERSION', '2024-10-21'),
            'deployment' => env('AZURE_OPENAI_DEPLOYMENT', 'gpt-4o'),
            'embedding_deployment' => env('AZURE_OPENAI_EMBEDDING_DEPLOYMENT', 'text-embedding-3-small'),
        ],

        'cohere' => [
            'driver' => 'cohere',
            'key' => env('COHERE_API_KEY'),
        ],

        'deepseek' => [
            'driver' => 'deepseek',
            'key' => env('DEEPSEEK_API_KEY'),
        ],

        'eleven' => [
            'driver' => 'eleven',
            'key' => env('ELEVENLABS_API_KEY'),
        ],

        'gemini' => [
            'driver' => 'gemini',
            'key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        ],

        'groq' => [
            'driver' => 'groq',
            'key' => env('GROQ_API_KEY'),
        ],

        'jina' => [
            'driver' => 'jina',
            'key' => env('JINA_API_KEY'),
        ],

        'mistral' => [
            'driver' => 'mistral',
            'key' => env('MISTRAL_API_KEY'),
        ],

        'ollama' => [
            'driver' => 'ollama',
            'key' => env('OLLAMA_API_KEY', ''),
            'url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
        ],

        'openai' => [
            'driver' => 'openai',
            'key' => env('OPENAI_API_KEY'),
        ],

        'openrouter' => [
            'driver' => 'openrouter',
            'key' => env('OPENROUTER_API_KEY'),
        ],

        'voyageai' => [
            'driver' => 'voyageai',
            'key' => env('VOYAGEAI_API_KEY'),
        ],

        'xai' => [
            'driver' => 'xai',
            'key' => env('XAI_API_KEY'),
        ],
    ],
];
