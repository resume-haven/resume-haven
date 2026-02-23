<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnalyzeController extends Controller
{
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'job_text' => ['required', 'min:30'],
            'cv_text' => ['required', 'min:30'],
        ]);

        // Placeholder for later KI analysis
        // $result = ...

        return view('result', [
            'job_text' => $validated['job_text'],
            'cv_text' => $validated['cv_text'],
            // 'result' => $result,
        ]);
    }
}
