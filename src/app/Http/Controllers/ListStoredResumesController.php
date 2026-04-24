<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Profile\Dto\StoredResumePageDto;
use App\Domains\Profile\Queries\ListStoredResumesQuery;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class ListStoredResumesController extends Controller
{
    public function __invoke(Request $request, Dispatcher $dispatcher): View
    {
        $authId = Auth::id();
        $userId = is_int($authId) ? $authId : (is_string($authId) && ctype_digit($authId) ? (int) $authId : null);

        if ($userId === null) {
            abort(403);
        }

        $currentToken = $request->session()->get('resume_token');
        $currentToken = is_string($currentToken) && $currentToken !== '' ? $currentToken : null;

        $page = max((int) $request->integer('page', 1), 1);

        /** @var StoredResumePageDto $resumePage */
        $resumePage = $dispatcher->dispatch(new ListStoredResumesQuery(
            userId: $userId,
            page: $page,
            perPage: 10,
            currentToken: $currentToken,
        ));

        return view('profile.index', $resumePage->toArray());
    }
}
