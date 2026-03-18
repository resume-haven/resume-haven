<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use Illuminate\Http\Request;

final class ResolveBaselineKeyAction
{
    public function execute(Request $request): string
    {
        $resumeToken = $request->session()->get('resume_token');

        if (is_string($resumeToken) && $resumeToken !== '') {
            return 'token:'.$resumeToken;
        }

        return 'session:'.$request->session()->getId();
    }
}
