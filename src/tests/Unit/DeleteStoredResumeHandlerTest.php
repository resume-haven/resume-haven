<?php

declare(strict_types=1);

use App\Domains\Profile\Commands\DeleteStoredResumeCommand;
use App\Domains\Profile\Handlers\DeleteStoredResumeHandler;
use App\Domains\Profile\Repositories\ProfileRepository;

describe('DeleteStoredResumeHandler', function (): void {
    test('deletes resume by token via repository', function (): void {
        $repository = Mockery::mock(ProfileRepository::class);
        $repository->shouldReceive('deleteByToken')
            ->once()
            ->with('DELETE-TOKEN-123');

        $handler = new DeleteStoredResumeHandler($repository);

        $handler->handle(new DeleteStoredResumeCommand('DELETE-TOKEN-123'));

        expect(true)->toBeTrue();
    });
});
