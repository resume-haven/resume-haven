<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final readonly class ResumeStatus
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';

    public string $value;

    public function __construct(string $value)
    {
        $normalized = strtolower(trim($value));

        if (!in_array($normalized, self::allowed(), true)) {
            throw new \InvalidArgumentException('Invalid resume status.');
        }

        $this->value = $normalized;
    }

    /**
     * @return list<string>
     */
    public static function allowed(): array
    {
        return [self::DRAFT, self::PUBLISHED, self::ARCHIVED];
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }
}
