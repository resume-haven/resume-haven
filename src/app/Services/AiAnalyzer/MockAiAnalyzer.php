<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;

/**
 * Mock AI Analyzer für Entwicklung ohne API-Limits
 *
 * Gibt vordefinierte, realistische Test-Daten zurück
 * Verschiedene Szenarien über Config steuerbar
 */
class MockAiAnalyzer implements AiAnalyzerInterface
{
    private string $scenario;
    private int $delayMs;

    public function __construct()
    {
        $scenario = config('ai.mock.scenario', 'realistic');
        if (! is_string($scenario)) {
            $scenario = 'realistic';
        }
        $this->scenario = $scenario;

        $delayConfig = config('ai.mock.delay_ms', 500);
        $delay = 500;
        if (is_int($delayConfig)) {
            $delay = $delayConfig;
        } elseif (is_numeric($delayConfig)) {
            $delay = (int) $delayConfig;
        }
        $this->delayMs = $delay;
    }

    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto
    {
        // Simuliere API-Delay
        if ($this->delayMs > 0) {
            usleep($this->delayMs * 1000);
        }

        // Wähle Szenario
        $data = match ($this->scenario) {
            'high_score' => $this->getHighScoreScenario(),
            'low_score' => $this->getLowScoreScenario(),
            'no_match' => $this->getNoMatchScenario(),
            default => $this->getRealisticScenario(),
        };

        return new AnalyzeResultDto(
            job_text: $request->jobText(),
            cv_text: $request->cvText(),
            requirements: $data['requirements'],
            experiences: $data['experiences'],
            matches: $data['matches'],
            gaps: $data['gaps'],
            error: null,
            tags: $data['tags']
        );
    }

    public function isAvailable(): bool
    {
        return true; // Mock ist immer verfügbar
    }

    public function getProviderName(): string
    {
        return 'mock';
    }

    /**
     * Realistic Scenario: Ausgeglichenes Ergebnis (60% Score)
     *
     * @return array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, tags: array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>}}
     */
    private function getRealisticScenario(): array
    {
        return [
            'requirements' => [
                'PHP 8+ Kenntnisse',
                'Laravel Framework Erfahrung',
                'RESTful API Design',
                'MySQL/PostgreSQL Datenbanken',
                'Git Versionskontrolle',
            ],
            'experiences' => [
                '5 Jahre PHP Entwicklung',
                'Mehrere Laravel Projekte',
                'API Entwicklung und Integration',
                'Datenbank-Design',
            ],
            'matches' => [
                ['requirement' => 'PHP 8+ Kenntnisse', 'experience' => '5 Jahre PHP Entwicklung'],
                ['requirement' => 'Laravel Framework Erfahrung', 'experience' => 'Mehrere Laravel Projekte'],
                ['requirement' => 'RESTful API Design', 'experience' => 'API Entwicklung und Integration'],
            ],
            'gaps' => [
                'MySQL/PostgreSQL Datenbanken',
                'Git Versionskontrolle',
            ],
            'tags' => [
                'matches' => [
                    ['requirement' => 'Softwareentwicklung', 'experience' => ['5 Jahre PHP Entwicklung']],
                    ['requirement' => 'Backend', 'experience' => ['Laravel Projekte', 'API Entwicklung']],
                    ['requirement' => 'Datenbanken', 'experience' => ['Datenbank-Design']],
                ],
                'gaps' => [
                    'MySQL/PostgreSQL',
                    'Git',
                ],
            ],
        ];
    }

    /**
     * High Score Scenario: Sehr gute Übereinstimmung (90% Score)
     *
     * @return array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, tags: array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>}}
     */
    private function getHighScoreScenario(): array
    {
        return [
            'requirements' => [
                'PHP 8+ Kenntnisse',
                'Laravel Framework',
                'RESTful API Design',
                'MySQL Datenbanken',
                'Git Versionskontrolle',
                'Docker Container',
                'TDD/Testing',
                'Agile Methodologie',
                'Clean Code Prinzipien',
                'Code Reviews',
            ],
            'experiences' => [
                '8 Jahre PHP Entwicklung',
                'Senior Laravel Developer',
                'RESTful API Architekt',
                'MySQL Performance Tuning',
                'Git Flow Expertise',
                'Docker & Kubernetes',
                'Test-Driven Development',
                'Agile/Scrum Teams',
                'Clean Architecture',
            ],
            'matches' => [
                ['requirement' => 'PHP 8+ Kenntnisse', 'experience' => '8 Jahre PHP Entwicklung'],
                ['requirement' => 'Laravel Framework', 'experience' => 'Senior Laravel Developer'],
                ['requirement' => 'RESTful API Design', 'experience' => 'RESTful API Architekt'],
                ['requirement' => 'MySQL Datenbanken', 'experience' => 'MySQL Performance Tuning'],
                ['requirement' => 'Git Versionskontrolle', 'experience' => 'Git Flow Expertise'],
                ['requirement' => 'Docker Container', 'experience' => 'Docker & Kubernetes'],
                ['requirement' => 'TDD/Testing', 'experience' => 'Test-Driven Development'],
                ['requirement' => 'Agile Methodologie', 'experience' => 'Agile/Scrum Teams'],
                ['requirement' => 'Clean Code Prinzipien', 'experience' => 'Clean Architecture'],
            ],
            'gaps' => [
                'Code Reviews',
            ],
            'tags' => [
                'matches' => [
                    ['requirement' => 'Backend', 'experience' => ['8 Jahre PHP', 'Senior Laravel', 'API Architekt']],
                    ['requirement' => 'Datenbanken', 'experience' => ['MySQL Performance']],
                    ['requirement' => 'DevOps', 'experience' => ['Docker', 'Kubernetes']],
                    ['requirement' => 'Qualität', 'experience' => ['TDD', 'Clean Architecture']],
                    ['requirement' => 'Methoden', 'experience' => ['Agile/Scrum']],
                ],
                'gaps' => [
                    'Code Reviews',
                ],
            ],
        ];
    }

    /**
     * Low Score Scenario: Geringe Übereinstimmung (25% Score)
     *
     * @return array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, tags: array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>}}
     */
    private function getLowScoreScenario(): array
    {
        return [
            'requirements' => [
                'PHP 8+ Kenntnisse',
                'Laravel Framework',
                'RESTful API Design',
                'MySQL Datenbanken',
                'Git Versionskontrolle',
                'Docker Container',
                'TDD/Testing',
                'Microservices Architektur',
            ],
            'experiences' => [
                'HTML & CSS Entwicklung',
                'JavaScript Frontend',
                'WordPress Theme Development',
            ],
            'matches' => [
                ['requirement' => 'PHP 8+ Kenntnisse', 'experience' => 'WordPress Theme Development'],
                ['requirement' => 'Git Versionskontrolle', 'experience' => 'HTML & CSS Entwicklung'],
            ],
            'gaps' => [
                'Laravel Framework',
                'RESTful API Design',
                'MySQL Datenbanken',
                'Docker Container',
                'TDD/Testing',
                'Microservices Architektur',
            ],
            'tags' => [
                'matches' => [
                    ['requirement' => 'PHP', 'experience' => ['WordPress']],
                    ['requirement' => 'Version Control', 'experience' => ['Git Basics']],
                ],
                'gaps' => [
                    'Laravel',
                    'APIs',
                    'Datenbanken',
                    'Docker',
                    'Testing',
                    'Microservices',
                ],
            ],
        ];
    }

    /**
     * No Match Scenario: Keine Übereinstimmungen (0% Score)
     *
     * @return array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, tags: array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>}}
     */
    private function getNoMatchScenario(): array
    {
        return [
            'requirements' => [
                'Python Django Framework',
                'Machine Learning',
                'TensorFlow',
                'Data Science',
                'AWS Cloud Infrastructure',
            ],
            'experiences' => [
                'PHP Laravel Entwicklung',
                'MySQL Datenbank-Design',
                'Frontend Development',
                'WordPress Themes',
            ],
            'matches' => [],
            'gaps' => [
                'Python Django Framework',
                'Machine Learning',
                'TensorFlow',
                'Data Science',
                'AWS Cloud Infrastructure',
            ],
            'tags' => [
                'matches' => [],
                'gaps' => [
                    'Python',
                    'Django',
                    'ML',
                    'TensorFlow',
                    'Data Science',
                    'AWS',
                ],
            ],
        ];
    }
}
