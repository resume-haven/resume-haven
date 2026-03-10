<?php

declare(strict_types=1);

namespace App\Domains\Profile\Handlers;

use App\Domains\Profile\Actions\EncryptResumeAction;
use App\Domains\Profile\Actions\GenerateTokenAction;
use App\Domains\Profile\Commands\StoreResumeCommand;
use App\Domains\Profile\Dto\ResumeTokenDto;
use App\Domains\Profile\Repositories\ProfileRepository;

class StoreResumeHandler
{
    public function __construct(
        private GenerateTokenAction $generateToken,
        private EncryptResumeAction $encryptResume,
        private ProfileRepository $repository,
    ) {}

    public function handle(StoreResumeCommand $command): ResumeTokenDto
    {
        $token = $this->generateUniqueToken();
        $encryptedCv = $this->encryptResume->execute($command->request->cvText, $token);

        $this->repository->store($token, $encryptedCv);

        return new ResumeTokenDto($token);
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = $this->generateToken->execute();
        } while ($this->repository->existsByToken($token));

        return $token;
    }
}
