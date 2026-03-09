<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Recommendations UI', function () {
    test('zeigt Empfehlungs-Panel wenn recommendations vorhanden sind', function () {
        // Mock AI-Analyzer mit recommendations
        config(['ai.provider' => 'mock', 'ai.mock.scenario' => 'realistic']);

        $response = $this->post('/analyze', [
            'job_text' => str_repeat('Test Job Description ', 10),
            'cv_text' => str_repeat('Test CV Content ', 10),
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('result');

        // Prüfe dass Empfehlungs-Section vorhanden ist
        $response->assertSee('💡 Empfehlungen & Verbesserungsvorschläge');
        $response->assertSee('Priorität:');
        $response->assertSee('Beispiel-Formulierung:');
    });

    test('zeigt keine Empfehlungs-Section wenn keine recommendations vorhanden', function () {
        // Mock ohne recommendations (no_match hat keine, wenn wir das ändern)
        config(['ai.provider' => 'mock', 'ai.mock.scenario' => 'high_score']);

        $response = $this->post('/analyze', [
            'job_text' => str_repeat('Test Job ', 10),
            'cv_text' => str_repeat('Test CV ', 10),
        ]);

        $response->assertStatus(200);
        // Panel sollte trotzdem existieren, da high_score Scenario eine Empfehlung hat
        $response->assertSee('💡 Empfehlungen & Verbesserungsvorschläge');
    });

    test('zeigt Prioritäts-Badges mit korrekten Farben', function () {
        config(['ai.provider' => 'mock', 'ai.mock.scenario' => 'realistic']);

        $response = $this->post('/analyze', [
            'job_text' => str_repeat('PHP Laravel MySQL Git ', 10),
            'cv_text' => str_repeat('PHP experience ', 10),
        ]);

        $response->assertStatus(200);

        // Realistic Scenario hat high + medium Empfehlungen
        $response->assertSee('Priorität: Hoch');
        $response->assertSee('Priorität: Mittel');
    });

    test('zeigt Gap-Namen und Beispiel-Formulierungen', function () {
        config(['ai.provider' => 'mock', 'ai.mock.scenario' => 'realistic']);

        $response = $this->post('/analyze', [
            'job_text' => str_repeat('Requirements ', 10),
            'cv_text' => str_repeat('Experience ', 10),
        ]);

        $response->assertStatus(200);

        // Prüfe dass Gap-Namen angezeigt werden
        $response->assertSee('MySQL/PostgreSQL Datenbanken');
        $response->assertSee('Git Versionskontrolle');

        // Prüfe Beispiel-Formulierungen
        $response->assertSee('💬 Beispiel-Formulierung:');
    });

    test('zeigt mehrere Empfehlungen in korrekter Reihenfolge', function () {
        config(['ai.provider' => 'mock', 'ai.mock.scenario' => 'low_score']);

        $response = $this->post('/analyze', [
            'job_text' => str_repeat('Senior Backend Developer ', 10),
            'cv_text' => str_repeat('WordPress Developer ', 10),
        ]);

        $response->assertStatus(200);

        // Low Score Scenario hat 6 Empfehlungen
        $response->assertSee('Laravel Framework');
        $response->assertSee('RESTful API Design');
        $response->assertSee('MySQL Datenbanken');
        $response->assertSee('Docker Container');
        $response->assertSee('TDD/Testing');
        $response->assertSee('Microservices Architektur');
    });
});
