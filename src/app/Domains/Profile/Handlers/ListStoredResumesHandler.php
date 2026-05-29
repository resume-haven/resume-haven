<?php

declare(strict_types=1);

namespace App\Domains\Profile\Handlers;

use App\Domains\Profile\Actions\DecryptResumeAction;
use App\Domains\Profile\Dto\StoredResumeListItemDto;
use App\Domains\Profile\Dto\StoredResumePageDto;
use App\Domains\Profile\Queries\ListStoredResumesQuery;
use App\Domains\Profile\Repositories\ProfileRepository;
use App\Models\StoredResume;

final class ListStoredResumesHandler
{
    public function __construct(
        private ProfileRepository $repository,
        private DecryptResumeAction $decryptResume,
    ) {}

    public function handle(ListStoredResumesQuery $query): StoredResumePageDto
    {
        $paginator = $this->repository->paginateByUser(
            userId: $query->userId,
            perPage: $query->perPage,
            page: $query->page,
            search: $query->search,
            sort: $query->sort,
            direction: $query->direction,
        );

        $items = array_values(array_map(
            fn (StoredResume $resume): StoredResumeListItemDto => $this->mapResume($resume, $query->currentToken),
            $paginator->items(),
        ));

        return new StoredResumePageDto(
            items: $items,
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            search: $query->search,
            sort: $query->sort,
            direction: $query->direction,
        );
    }

    private function mapResume(StoredResume $resume, ?string $currentToken): StoredResumeListItemDto
    {
        $cvText = $this->decryptResume->execute($resume->encrypted_cv, $resume->token);
        $preview = $this->buildPreview($cvText);

        return new StoredResumeListItemDto(
            token: $resume->token,
            preview: $preview,
            updatedAt: $resume->updated_at->format('d.m.Y H:i'),
            isCurrent: $currentToken === $resume->token,
            fileName: $resume->file_name,
            originalFilename: $resume->original_filename,
        );
    }

    private function buildPreview(?string $cvText): string
    {
        if (! is_string($cvText) || $cvText === '') {
            return 'Vorschau nicht verfuegbar.';
        }

        $normalized = preg_replace('/\s+/u', ' ', trim($cvText)) ?? trim($cvText);

        if (mb_strlen($normalized) <= 140) {
            return $normalized;
        }

        return mb_substr($normalized, 0, 137).'...';
    }
}
