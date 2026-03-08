<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

/**
 * Controller für rechtliche Seiten (Impressum, Datenschutz, Lizenzen)
 *
 * Hinweis: Verwendet Named Methods statt Single-Action-Pattern,
 * da es sich um einfache statische Content-Seiten handelt.
 */
class LegalController extends Controller
{
    /**
     * Zeigt die Impressum-Seite
     */
    public function impressum(): View
    {
        return view('legal.impressum');
    }

    /**
     * Zeigt die Datenschutzerklärung
     */
    public function datenschutz(): View
    {
        return view('legal.datenschutz');
    }

    /**
     * Zeigt die Lizenzen-Seite mit automatisch generierten Daten
     */
    public function lizenzen(): View
    {
        $licenses = [
            'php' => [],
            'node' => [],
            'generated_at' => null,
        ];

        if (Storage::exists('licenses.json')) {
            $raw = Storage::get('licenses.json');

            if (is_string($raw) && $raw !== '') {
                $data = json_decode($raw, true);

                if (is_array($data)) {
                    $licenses = [
                        'php' => $data['php'] ?? [],
                        'node' => $data['node'] ?? [],
                        'generated_at' => $data['generated_at'] ?? null,
                    ];
                }
            }
        }

        return view('legal.lizenzen', $licenses);
    }
}
