<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Profile\Dto\StoredResumePageDto;
use App\Domains\Profile\Queries\ListStoredResumesQuery;
use App\Support\Session\ResumeTokenSession;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class ListStoredResumesController extends Controller
{
    public function __construct(
        private ResumeTokenSession $resumeTokenSession,
    ) {}

    public function __invoke(Request $request, Dispatcher $dispatcher): View
    {
        $authId = Auth::id();
        $userId = is_int($authId) ? $authId : (is_string($authId) && ctype_digit($authId) ? (int) $authId : null);

        if ($userId === null) {
            abort(403);
        }

        $currentToken = $this->resumeTokenSession->current($request->session());

        $page = max((int) $request->integer('page', 1), 1);
        $perPage = max((int) $request->integer('per_page', 10), 1);
        $search = $request->string('search')->trim()->value();
        $sort = $request->string('sort', 'updated_at')->value();
        $direction = $request->string('direction', 'desc')->value();

        /** @var StoredResumePageDto $resumePage */
        $resumePage = $dispatcher->dispatch(new ListStoredResumesQuery(
            userId: $userId,
            page: $page,
            perPage: $perPage,
            currentToken: $currentToken,
            search: $search !== '' ? $search : null,
            sort: $sort,
            direction: $direction,
        ));

        return view('profile.index', $resumePage->toArray());
    }
}
