<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use App\Domains\Profile\Dto\CompetenceResumeDto;

/**
 * Leitet aus einem CV-Text einen strukturierten Kompetenzlebenslauf ab.
 */
final class BuildCompetenceResumeAction
{
    /**
     * @var array<string, string>
     */
    private const HARD_SKILL_KEYWORDS = [
        'php' => 'PHP',
        'laravel' => 'Laravel',
        'symfony' => 'Symfony',
        'javascript' => 'JavaScript',
        'typescript' => 'TypeScript',
        'html' => 'HTML',
        'css' => 'CSS',
        'sql' => 'SQL',
        'mysql' => 'MySQL',
        'postgresql' => 'PostgreSQL',
        'postgres' => 'PostgreSQL',
        'redis' => 'Redis',
        'docker' => 'Docker',
        'kubernetes' => 'Kubernetes',
        'git' => 'Git',
        'rest' => 'REST API',
        'graphql' => 'GraphQL',
        'phpunit' => 'PHPUnit',
        'pest' => 'Pest',
        'tdd' => 'TDD',
        'ddd' => 'DDD',
        'cqrs' => 'CQRS',
        'clean architecture' => 'Clean Architecture',
        'linux' => 'Linux',
        'aws' => 'AWS',
        'azure' => 'Azure',
        'gcp' => 'GCP',
    ];

    /**
     * @var array<string, string>
     */
    private const SOFT_SKILL_KEYWORDS = [
        'kommunikation' => 'Kommunikation',
        'team' => 'Teamarbeit',
        'mentoring' => 'Mentoring',
        'lead' => 'Leadership',
        'agil' => 'Agiles Arbeiten',
        'scrum' => 'Scrum',
        'kanban' => 'Kanban',
        'probleml' => 'Problemloesung',
        'eigenverantwort' => 'Eigenverantwortung',
    ];

    /**
     * @var array<string, string>
     */
    private const DOMAIN_KEYWORDS = [
        'hr' => 'HR',
        'erp' => 'ERP',
        'saas' => 'SaaS',
        'finanz' => 'Finanzen',
        'e-commerce' => 'E-Commerce',
        'ecommerce' => 'E-Commerce',
        'health' => 'HealthTech',
        'logistik' => 'Logistik',
    ];

    public function execute(string $cvText): CompetenceResumeDto
    {
        $normalizedText = mb_strtolower($cvText);

        $hardSkills = $this->extractKeywordMatches($normalizedText, self::HARD_SKILL_KEYWORDS);
        $softSkills = $this->extractKeywordMatches($normalizedText, self::SOFT_SKILL_KEYWORDS);
        $domains = $this->extractKeywordMatches($normalizedText, self::DOMAIN_KEYWORDS);
        $yearsExperience = $this->extractYearsExperience($normalizedText);

        $summary = $this->buildSummary($hardSkills, $softSkills, $domains, $yearsExperience);

        return new CompetenceResumeDto(
            hardSkills: $hardSkills,
            softSkills: $softSkills,
            domains: $domains,
            yearsExperience: $yearsExperience,
            summary: $summary,
        );
    }

    /**
     * @param  array<string, string> $keywordMap
     * @return array<int, string>
     */
    private function extractKeywordMatches(string $text, array $keywordMap): array
    {
        $matches = [];

        foreach ($keywordMap as $needle => $label) {
            if (str_contains($text, $needle)) {
                $matches[] = $label;
            }
        }

        return array_values(array_unique($matches));
    }

    private function extractYearsExperience(string $text): ?int
    {
        if (preg_match_all('/(\d{1,2})\s*(?:\+|plus)?\s*(?:jahre|jahr|years|year)/iu', $text, $matches) < 1) {
            return null;
        }

        $years = array_map(static fn (string $value): int => (int) $value, $matches[1]);

        if ($years === []) {
            return null;
        }

        /** @var non-empty-array<int> $years */
        /** @var int $maxYears */
        $maxYears = max($years);

        return $maxYears;
    }

    /**
     * @param array<int, string> $hardSkills
     * @param array<int, string> $softSkills
     * @param array<int, string> $domains
     */
    private function buildSummary(array $hardSkills, array $softSkills, array $domains, ?int $yearsExperience): string
    {
        $parts = [];

        if ($yearsExperience !== null) {
            $parts[] = 'Berufserfahrung: '.$yearsExperience.'+ Jahre';
        }

        if ($hardSkills !== []) {
            $parts[] = 'Technischer Fokus: '.implode(', ', array_slice($hardSkills, 0, 6));
        }

        if ($softSkills !== []) {
            $parts[] = 'Staerken: '.implode(', ', array_slice($softSkills, 0, 4));
        }

        if ($domains !== []) {
            $parts[] = 'Domainen: '.implode(', ', array_slice($domains, 0, 4));
        }

        if ($parts === []) {
            return 'Es konnten noch keine klaren Kompetenzcluster erkannt werden. Bitte CV-Text erweitern.';
        }

        return implode(' | ', $parts);
    }
}
