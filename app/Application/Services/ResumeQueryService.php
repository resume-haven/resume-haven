<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Contracts\ResumeReadRepositoryInterface;
use App\Application\Contracts\ResumeStatusHistoryReadRepositoryInterface;
use App\Application\DTOs\ResumeDTO;
use App\Application\DTOs\ResumeStatusHistoryDTO;
use App\Application\ReadModels\ResumeStatusHistoryReadModel;

final class ResumeQueryService
{
    public function __construct(
        private ResumeReadRepositoryInterface $resumes,
        private ResumeStatusHistoryReadRepositoryInterface $history,
    ) {
    }

    public function getById(int $id): ?ResumeDTO
    {
        $resume = $this->resumes->findById($id);

        if ($resume === null) {
            return null;
        }

        return new ResumeDTO($resume->id, $resume->name, $resume->email, $resume->status);
    }

    /**
     * @return list<ResumeStatusHistoryDTO>|null
     */
    public function getStatusHistory(int $id): ?array
    {
        $resume = $this->resumes->findById($id);

        if ($resume === null) {
            return null;
        }

        return array_map(
            static fn (ResumeStatusHistoryReadModel $entry): ResumeStatusHistoryDTO => new ResumeStatusHistoryDTO(
                $entry->id,
                $entry->resume_id,
                $entry->from_status,
                $entry->to_status,
                $entry->changed_at,
            ),
            $this->history->listForResume($id),
        );
    }
}
