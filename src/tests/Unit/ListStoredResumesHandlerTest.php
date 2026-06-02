<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\DecryptResumeAction;
use App\Domains\Profile\Handlers\ListStoredResumesHandler;
use App\Domains\Profile\Queries\ListStoredResumesQuery;
use App\Domains\Profile\Repositories\ProfileRepository;
use App\Models\StoredResume;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

function makeStoredResume(string $token, string $encryptedCv, string $updatedAt, ?string $fileName = null, ?string $originalFilename = null): StoredResume
{
    $resume = new StoredResume();
    $resume->token = $token;
    $resume->encrypted_cv = $encryptedCv;
    $resume->updated_at = Carbon::parse($updatedAt);
    $resume->file_name = $fileName;
    $resume->original_filename = $originalFilename;

    return $resume;
}

describe('ListStoredResumesHandler', function (): void {
    test('maps paginator items to dto list with normalized preview and current token state', function (): void {
        $resumeA = makeStoredResume('TOKEN-A', 'enc-a', '2026-04-24 10:00:00', 'file-a.pdf', 'orig-a.pdf');
        $resumeB = makeStoredResume('TOKEN-B', 'enc-b', '2026-04-24 11:30:00');

        $paginator = new LengthAwarePaginator(
            items: [$resumeA, $resumeB],
            total: 12,
            perPage: 10,
            currentPage: 2,
        );

        $repository = Mockery::mock(ProfileRepository::class);
        $repository->shouldReceive('paginateByUser')
            ->once()
            ->with(5, 10, 2, 'search-term', 'updated_at', 'desc')
            ->andReturn($paginator);

        $decrypt = Mockery::mock(DecryptResumeAction::class);
        $decrypt->shouldReceive('execute')
            ->once()
            ->with('enc-a', 'TOKEN-A')
            ->andReturn("  Kurz   Text\nmit   Spaces  ");
        $decrypt->shouldReceive('execute')
            ->once()
            ->with('enc-b', 'TOKEN-B')
            ->andReturn(str_repeat('L', 160));

        $handler = new ListStoredResumesHandler($repository, $decrypt);

        $result = $handler->handle(new ListStoredResumesQuery(
            userId: 5,
            page: 2,
            perPage: 10,
            currentToken: 'TOKEN-B',
            search: 'search-term',
        ));

        expect($result->currentPage)->toBe(2)
            ->and($result->lastPage)->toBe(2)
            ->and($result->perPage)->toBe(10)
            ->and($result->total)->toBe(12)
            ->and($result->items)->toHaveCount(2)
            ->and($result->items[0]->preview)->toBe('Kurz Text mit Spaces')
            ->and($result->items[0]->isCurrent)->toBeFalse()
            ->and($result->items[0]->fileName)->toBe('file-a.pdf')
            ->and($result->items[0]->originalFilename)->toBe('orig-a.pdf')
            ->and($result->items[1]->preview)->toHaveLength(140)
            ->and($result->items[1]->preview)->toEndWith('...')
            ->and($result->items[1]->isCurrent)->toBeTrue();
    });

    test('uses fallback preview when decrypted text is invalid', function (): void {
        $resume = makeStoredResume('TOKEN-FALLBACK', 'enc-fallback', '2026-04-24 12:00:00');

        $paginator = new LengthAwarePaginator(
            items: [$resume],
            total: 1,
            perPage: 10,
            currentPage: 1,
        );

        $repository = Mockery::mock(ProfileRepository::class);
        $repository->shouldReceive('paginateByUser')
            ->once()
            ->with(9, 10, 1, null, 'updated_at', 'desc')
            ->andReturn($paginator);

        $decrypt = Mockery::mock(DecryptResumeAction::class);
        $decrypt->shouldReceive('execute')
            ->once()
            ->with('enc-fallback', 'TOKEN-FALLBACK')
            ->andReturn(null);

        $handler = new ListStoredResumesHandler($repository, $decrypt);

        $result = $handler->handle(new ListStoredResumesQuery(userId: 9));

        expect($result->items)->toHaveCount(1)
            ->and($result->items[0]->preview)->toBe('Vorschau nicht verfuegbar.')
            ->and($result->items[0]->isCurrent)->toBeFalse();
    });
});
