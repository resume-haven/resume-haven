<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\GenerateTagsUseCase;

/**
 * Action: Generiere Tags aus Matches und Gaps
 *
 * Fallback: Falls die AI keine Tags liefert, wird diese Action
 * verwendet, um programmatisch Tags aus den bereits vorhanden
 * Matches und Gaps zu generieren.
 */
class GenerateTagsAction
{
    /**
     * Generiere Tags aus Matches und Gaps
     *
     * @param array<int, array{requirement: string, experience: string}> $matches
     * @param array<int, string> $gaps
     * @return array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>}
     */
    public function execute(array $matches, array $gaps): array
    {
        return [
            'matches' => $this->generateMatchTags($matches),
            'gaps' => $gaps, // Gaps sind ohnehin Strings
        ];
    }

    /**
     * Gruppiere Matches nach Requirement
     *
     * @param array<int, array{requirement: string, experience: string}> $matches
     * @return array<int, array{requirement: string, experience: array<string>}>
     */
    private function generateMatchTags(array $matches): array
    {
        // Gruppiere Matches nach Requirement
        /** @var array<string, array<string>> $grouped */
        $grouped = [];

        foreach ($matches as $match) {
            $requirement = $match['requirement'];
            $experience = $match['experience'];

            if (! isset($grouped[$requirement])) {
                $grouped[$requirement] = [];
            }

            $grouped[$requirement][] = $experience;
        }

        // Konvertiere zu TagMatchDto Format
        $result = [];
        foreach ($grouped as $requirement => $experiences) {
            $result[] = [
                'requirement' => $requirement,
                'experience' => $experiences,
            ];
        }

        return $result;
    }
}

