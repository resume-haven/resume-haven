<?php

declare(strict_types=1);

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Tests\Unit\MockableGeminiAiAnalyzer;

function mockableAnalyzer(): MockableGeminiAiAnalyzer
{
    return new MockableGeminiAiAnalyzer(
        new ValidateAiResponseAction(),
        new ParseAiResponseAction(),
    );
}

describe('GeminiAiAnalyzer Coverage', function () {
    test('analyze() happy path liefert AnalyzeResultDto ohne Fehler bei valider KI-Antwort', function () {
        $responseData = [
            'requirements'    => ['PHP-Kenntnisse'],
            'experiences'     => ['3 Jahre PHP-Entwicklung'],
            'matches'         => [['requirement' => 'PHP-Kenntnisse', 'experience' => '3 Jahre PHP-Entwicklung']],
            'gaps'            => [],
            'tags'            => ['matches' => [], 'gaps' => []],
            'recommendations' => [],
        ];

        $mockResponse = Mockery::mock(StructuredAgentResponse::class);
        $mockResponse->shouldReceive('toArray')->once()->andReturn($responseData);

        $mockAnalyzer = Mockery::mock(Analyzer::class);
        $mockAnalyzer->shouldReceive('prompt')->once()->andReturn($mockResponse);

        $analyzer = mockableAnalyzer();
        $analyzer->injectedAnalyzer = $mockAnalyzer;

        $result = $analyzer->analyze(new AnalyzeRequestDto('Wir suchen PHP-Entwickler', 'Ich habe 3 Jahre PHP-Erfahrung'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($result->error)->toBeNull();
        expect($result->requirements)->toBe(['PHP-Kenntnisse']);
        expect($result->experiences)->toBe(['3 Jahre PHP-Entwicklung']);
        expect($result->matches)->toHaveCount(1);
        expect($result->gaps)->toBe([]);
    });

    test('analyze() serialisiert und sanitiziert Request-Daten fuer den AI-Prompt', function () {
        $responseData = [
            'requirements' => [],
            'experiences' => [],
            'matches' => [],
            'gaps' => [],
            'tags' => ['matches' => [], 'gaps' => []],
            'recommendations' => [],
        ];

        $mockResponse = Mockery::mock(StructuredAgentResponse::class);
        $mockResponse->shouldReceive('toArray')->once()->andReturn($responseData);

        $mockAnalyzer = Mockery::mock(Analyzer::class);
        $mockAnalyzer->shouldReceive('prompt')->once()->withArgs(function (string $payload): bool {
            $decodedPayload = json_decode($payload, true);

            expect($decodedPayload)->toBe([
                'job_text' => "Jobtitel\nmit Zeilenumbruch",
                'cv_text' => 'CV mit Nullbyte',
            ]);

            return true;
        })->andReturn($mockResponse);

        $analyzer = mockableAnalyzer();
        $analyzer->injectedAnalyzer = $mockAnalyzer;

        $result = $analyzer->analyze(new AnalyzeRequestDto("  Jobtitel\r\nmit Zeilenumbruch  ", "\0CV mit Nullbyte\0  "));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($result->error)->toBeNull();
    });

    test('analyze() happy path uebergibt Recommendations und Gaps korrekt', function () {
        $responseData = [
            'requirements'    => ['Laravel'],
            'experiences'     => ['Laravel seit 5 Jahren'],
            'matches'         => [],
            'gaps'            => ['Docker-Kenntnisse'],
            'tags'            => ['matches' => [], 'gaps' => ['Docker-Kenntnisse']],
            'recommendations' => [
                [
                    'gap'            => 'Docker-Kenntnisse',
                    'priority'       => 'high',
                    'suggestion'     => 'Docker-Kurs absolvieren',
                    'example_phrase' => 'Grundlegende Docker-Kenntnisse vorhanden',
                ],
            ],
        ];

        $mockResponse = Mockery::mock(StructuredAgentResponse::class);
        $mockResponse->shouldReceive('toArray')->once()->andReturn($responseData);

        $mockAnalyzer = Mockery::mock(Analyzer::class);
        $mockAnalyzer->shouldReceive('prompt')->once()->andReturn($mockResponse);

        $analyzer = mockableAnalyzer();
        $analyzer->injectedAnalyzer = $mockAnalyzer;

        $result = $analyzer->analyze(new AnalyzeRequestDto('Laravel-Job', 'Senior-CV'));

        expect($result->error)->toBeNull();
        expect($result->gaps)->toBe(['Docker-Kenntnisse']);
        expect($result->recommendations)->toHaveCount(1);
    });

    test('callAi() faengt RuntimeException wenn Response-JSON-Encoding fehlschlaegt', function () {
        // toArray() liefert Array mit ungültigem UTF-8 → json_encode gibt false zurück
        $mockResponse = Mockery::mock(StructuredAgentResponse::class);
        $mockResponse->shouldReceive('toArray')->once()->andReturn(['key' => "\xB1\x31"]);

        $mockAnalyzer = Mockery::mock(Analyzer::class);
        $mockAnalyzer->shouldReceive('prompt')->once()->andReturn($mockResponse);

        Log::shouldReceive('error')->once();

        $analyzer = mockableAnalyzer();
        $analyzer->injectedAnalyzer = $mockAnalyzer;

        $result = $analyzer->analyze(new AnalyzeRequestDto('Job', 'CV'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($result->error)->not()->toBeNull();
        expect($result->requirements)->toBe([]);
        expect($result->experiences)->toBe([]);
    });

    test('createAnalyzer() gibt Analyzer-Instanz zurueck wenn kein Mock injiziert ist', function () {
        $analyzer = mockableAnalyzer();

        $result = $analyzer->exposedCreateAnalyzer();

        expect($result)->toBeInstanceOf(Analyzer::class);
    });
});
