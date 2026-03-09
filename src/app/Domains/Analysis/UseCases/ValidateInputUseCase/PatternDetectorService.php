<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ValidateInputUseCase;

/**
 * Ermittelt verdaechtige Pattern im Input.
 */
class PatternDetectorService
{
    /**
     * @var array<string, string>
     */
    private const PATTERNS = [
        '/\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION|ALTER|CREATE|EXECUTE|EXEC)\b/i' => 'SQL Keywords',
        '/<script[^>]*>.*?<\/script>/is' => 'Script Tags',
        '/on\w+\s*=/i' => 'Event Handlers',
        '/<iframe/i' => 'iFrame Tags',
        '/<object/i' => 'Object Tags',
        '/<embed/i' => 'Embed Tags',
    ];

    /**
     * @return array<int, string>
     */
    public function detect(string $input): array
    {
        $detected = [];

        foreach (self::PATTERNS as $regex => $label) {
            if (preg_match($regex, $input) === 1) {
                $detected[] = $label;
            }
        }

        return array_values(array_unique($detected));
    }
}
