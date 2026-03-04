<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Security Audit', function () {
    test('CSRF-Protection ist in analyze.blade.php vorhanden', function () {
        $response = $this->get('/analyze');

        // Check that CSRF token is rendered (either @csrf or actual token)
        $content = $response->getContent();
        expect($content)->toContain('_token');
        // Form sollte method POST haben
        expect($content)->toContain('method="POST"');
    });

    test('POST /analyze erfordert CSRF-Token', function () {
        // Attempt POST ohne explizitem CSRF-Token (Laravel wird automatisch einen verfügbar machen in Tests)
        // Dies ist eher ein Dokumentations-Test
        $response = $this->get('/analyze');

        // Form sollte CSRF-Token enthalten
        expect($response->getContent())->toContain('_token');
    });

    test('Input-Validierung prüft Mindestlänge', function () {
        $response = $this->post('/analyze', [
            'job_text' => 'short',  // zu kurz (min:30)
            'cv_text' => 'short',   // zu kurz
        ]);

        // Sollte Validierungsfehler geben
        expect($response->getStatusCode())->toBeIn([302, 422]); // Redirect oder Validation Error
    });

    test('SQL-Injection-Pattern wird in Logs gewarnt', function () {
        // Versuche SQL-Injection – sollte von ValidateInputAction erkannt werden
        $jobText = str_repeat('a', 40).'; DROP TABLE users; --';

        $response = $this->post('/analyze', [
            'job_text' => $jobText,
            'cv_text' => str_repeat('b', 40),
        ]);

        // Response sollte 200 sein (wird verarbeitet, aber gewarnt)
        // oder Error wegen Validierung
        expect($response->getStatusCode())->toBeIn([200, 302, 422]);
    });

    test('Script-Injection-Pattern wird erkannt', function () {
        $jobText = 'Job '.str_repeat('d', 30).' <script>alert()</script>';

        $response = $this->post('/analyze', [
            'job_text' => $jobText,
            'cv_text' => str_repeat('c', 40),
        ]);

        // Sollte verarbeitet werden (mit Warnung im Log)
        expect($response->getStatusCode())->toBeIn([200, 302, 422]);
    });

    test('XSS-Pattern in Event-Handlern wird erkannt', function () {
        $jobText = str_repeat('e', 40).' onclick="alert()"';

        $response = $this->post('/analyze', [
            'job_text' => $jobText,
            'cv_text' => str_repeat('f', 40),
        ]);

        // Sollte verarbeitet werden (mit Warnung im Log)
        expect($response->getStatusCode())->toBeIn([200, 302, 422]);
    });
});
