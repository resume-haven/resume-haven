<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ShowResultController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        /** @var mixed $viewData */
        $viewData = $request->session()->get('analysis_result_view_data');

        if (! is_array($viewData)) {
            return redirect()
                ->route('analyze')
                ->withErrors([
                    'result' => 'Kein gespeichertes Analyse-Ergebnis mehr verfuegbar. Bitte fuehre die Analyse erneut aus, um den Claim-Flow fortzusetzen.',
                ]);
        }

        return view('result', $viewData);
    }
}
