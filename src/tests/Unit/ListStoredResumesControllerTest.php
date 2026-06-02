<?php

declare(strict_types=1);

use App\Domains\Profile\Dto\StoredResumeListItemDto;
use App\Domains\Profile\Dto\StoredResumePageDto;
use App\Domains\Profile\Queries\ListStoredResumesQuery;
use App\Http\Controllers\ListStoredResumesController;
use App\Support\Session\ResumeTokenSession;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

function makeProfileRequest(int $page, mixed $resumeToken, mixed $resumeTokens = null, ?string $search = null): Request
{
    $request = Request::create('/profile', 'GET', ['page' => $page, 'search' => $search]);

    /** @var Store $session */
    $session = app('session')->driver('array');
    $session->start();
    $session->put('resume_token', $resumeToken);

    if ($resumeTokens !== null) {
        $session->put('resume_tokens', $resumeTokens);
    }

    $request->setLaravelSession($session);

    return $request;
}

describe('ListStoredResumesController', function (): void {
    test('dispatches query with normalized auth id, token, page and search and returns profile view', function (): void {
        Auth::shouldReceive('id')->once()->andReturn('42');

        $request = makeProfileRequest(-3, 'TOKEN-42', null, '  my search  ');

        $pageDto = new StoredResumePageDto(
            items: [
                new StoredResumeListItemDto(
                    token: 'TOKEN-42',
                    preview: 'Kurzprofil',
                    updatedAt: '24.04.2026 12:30',
                    isCurrent: true,
                    fileName: 'resume.pdf'
                ),
            ],
            currentPage: 1,
            lastPage: 1,
            perPage: 10,
            total: 1,
            search: 'my search'
        );

        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (ListStoredResumesQuery $query): bool {
                return $query->userId === 42
                    && $query->page === 1
                    && $query->perPage === 10
                    && $query->currentToken === 'TOKEN-42'
                    && $query->search === 'my search';
            })
            ->andReturn($pageDto);

        $controller = new ListStoredResumesController(new ResumeTokenSession());
        $view = $controller($request, $dispatcher);

        expect($view->name())->toBe('profile.index')
            ->and($view->getData()['items'])->toHaveCount(1)
            ->and($view->getData()['pagination']['current_page'])->toBe(1)
            ->and($view->getData()['search'])->toBe('my search');
    });

    test('normalizes non-string session token to null before dispatching', function (): void {
        Auth::shouldReceive('id')->once()->andReturn(7);

        $request = makeProfileRequest(3, ['invalid-token-type']);

        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (ListStoredResumesQuery $query): bool {
                return $query->userId === 7
                    && $query->page === 3
                    && $query->currentToken === null;
            })
            ->andReturn(new StoredResumePageDto(items: [], currentPage: 3, lastPage: 3, perPage: 10, total: 0));

        $controller = new ListStoredResumesController(new ResumeTokenSession());
        $controller($request, $dispatcher);

        expect(true)->toBeTrue();
    });

    test('aborts with 403 when auth id is not numeric', function (): void {
        Auth::shouldReceive('id')->once()->andReturn('nope');

        $request = makeProfileRequest(1, null);
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldNotReceive('dispatch');

        $controller = new ListStoredResumesController(new ResumeTokenSession());

        try {
            $controller($request, $dispatcher);
            throw new RuntimeException('Expected a 403 HttpException.');
        } catch (HttpException $exception) {
            expect($exception->getStatusCode())->toBe(403);
        }
    });

    test('uses latest token from resume_tokens when current key is missing', function (): void {
        Auth::shouldReceive('id')->once()->andReturn(8);

        $request = makeProfileRequest(1, null, ['TOKEN-OLD', 'TOKEN-NEW']);

        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (ListStoredResumesQuery $query): bool {
                return $query->userId === 8
                    && $query->page === 1
                    && $query->currentToken === 'TOKEN-NEW';
            })
            ->andReturn(new StoredResumePageDto(items: [], currentPage: 1, lastPage: 1, perPage: 10, total: 0));

        $controller = new ListStoredResumesController(new ResumeTokenSession());
        $controller($request, $dispatcher);

        expect(true)->toBeTrue();
    });
});
