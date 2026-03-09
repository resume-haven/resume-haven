<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Responsive Design', function () {
    test('Homepage lädt und wird korrekt dargestellt', function () {
        $response = $this->get('/');

        expect($response->getStatusCode())->toBe(200);
        // Check that page has content
        $content = $response->getContent();
        expect($content)->not()->toBeEmpty();
        // Should have HTML structure
        expect($content)->toContain('<html');
        expect($content)->toContain('</html>');
    });

    test('Analyze-Seite hat responsive Layout-Klassen', function () {
        $response = $this->get('/analyze');

        expect($response->getStatusCode())->toBe(200);
        // Check for responsive sizing
        expect($response->getContent())->toContain('sm:text');
        expect($response->getContent())->toContain('md:');
    });

    test('Result-Seite hat responsive Grid-Layout', function () {
        $response = $this->post('/analyze', [
            'job_text' => str_repeat('a', 40),
            'cv_text' => str_repeat('b', 40),
        ]);

        expect($response->getStatusCode())->toBeIn([200, 302]);
        // Check for responsive grid classes
        $content = $response->getContent();
        expect($content)->toContain('grid');
        expect($content)->toContain('md:');
    });

    test('Dark-Mode CSS-Klassen sind vorhanden', function () {
        $response = $this->get('/analyze');

        // Check for dark mode classes
        expect($response->getContent())->toContain('dark:');
        expect($response->getContent())->toContain('dark:bg-');
        expect($response->getContent())->toContain('dark:text-');
    });

    test('Typography ist auf allen Breakpoints responsive', function () {
        $response = $this->get('/analyze');

        // Check for responsive typography (mindestens sm und md)
        expect($response->getContent())->toContain('sm:text-');
        expect($response->getContent())->toContain('md:text-');
    });

    test('Spacing ist auf allen Breakpoints responsive', function () {
        $response = $this->get('/analyze');

        // Check for responsive spacing (wir verwenden sm:px- und sm:py- statt sm:p-)
        expect($response->getContent())->toContain('sm:px-');
        expect($response->getContent())->toContain('sm:py-');
        expect($response->getContent())->toContain('sm:space-y-');
    });

    test('Buttons sind auf Mobile-Geräten touchable', function () {
        $response = $this->get('/analyze');

        // Check minimum touch target size
        expect($response->getContent())->toContain('py-3');
        expect($response->getContent())->toContain('px-6');
    });
});
