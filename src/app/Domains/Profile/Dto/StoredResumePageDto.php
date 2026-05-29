<?php

declare(strict_types=1);

namespace App\Domains\Profile\Dto;

readonly class StoredResumePageDto
{
    /** @param list<StoredResumeListItemDto> $items */
    public function __construct(
        public array $items,
        public int $currentPage,
        public int $lastPage,
        public int $perPage,
        public int $total,
        public ?string $search = null,
        public string $sort = 'updated_at',
        public string $direction = 'desc',
    ) {}

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    public function previousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }

    public function nextPage(): ?int
    {
        return $this->hasNextPage() ? $this->currentPage + 1 : null;
    }

    /**
     * @return array{
     *     items: list<array{token: string, preview: string, updated_at: string, is_current: bool, file_name: ?string, original_filename: ?string}>,
     *     pagination: array{current_page: int, last_page: int, per_page: int, total: int, has_previous: bool, has_next: bool, previous_page: int|null, next_page: int|null},
     *     search: ?string,
     *     sort: string,
     *     direction: string
     * }
     */
    public function toArray(): array
    {
        return [
            'items' => array_map(
                static fn (StoredResumeListItemDto $item): array => $item->toArray(),
                $this->items,
            ),
            'pagination' => [
                'current_page' => $this->currentPage,
                'last_page' => $this->lastPage,
                'per_page' => $this->perPage,
                'total' => $this->total,
                'has_previous' => $this->hasPreviousPage(),
                'has_next' => $this->hasNextPage(),
                'previous_page' => $this->previousPage(),
                'next_page' => $this->nextPage(),
            ],
            'search' => $this->search,
            'sort' => $this->sort,
            'direction' => $this->direction,
        ];
    }
}
