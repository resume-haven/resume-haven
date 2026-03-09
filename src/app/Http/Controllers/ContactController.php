<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('legal.kontakt');
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        // MVP: Kein Mailversand, nur Logging der Anfrage.
        Log::info('Kontaktformular-Eingang', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message_preview' => mb_substr($validated['message'], 0, 120),
            'received_at' => now()->toIso8601String(),
        ]);

        return redirect()
            ->route('contact.show')
            ->with('success', 'Vielen Dank fuer Ihre Nachricht. Wir melden uns zeitnah.');
    }
}
