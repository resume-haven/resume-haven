<?php

declare(strict_types=1);

namespace App\Domains\Profile\Handlers;

use App\Domains\Profile\Actions\DecryptResumeAction;
use App\Domains\Profile\Dto\LoadedResumeDto;
use App\Domains\Profile\Queries\GetResumeByTokenQuery;
use App\Domains\Profile\Repositories\ProfileRepository;

class GetResumeByTokenHandler
{
    public function __construct(
        private ProfileRepository $repository,
        private DecryptResumeAction $decryptResume,
    ) {}

    public function handle(GetResumeByTokenQuery $query): ?LoadedResumeDto
    {
        $storedResume = $this->repository->getByToken($query->token);

        if ($storedResume === null) {
            return null;
        }

        $cvText = $this->decryptResume->execute($storedResume->encrypted_cv, $query->token);

        if ($cvText === null) {
            return null;
        }

        $this->repository->touchLastAccessedByToken($query->token);

        return new LoadedResumeDto($query->token, $cvText);
    }
}
