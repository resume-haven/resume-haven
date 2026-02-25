<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Pest-Style Tests: keine TestCase-Klasse, nur function()

test('validiert job_text und cv_text mit Mindestlänge', function () {
    $response = \Pest\Laravel\post('/analyze', [
        'job_text' => '',
        'cv_text' => '',
    ]);
    $response->assertSessionHasErrors(['job_text', 'cv_text']);
});


test('akzeptiert gültige Eingaben und zeigt die Ergebnis-View', function () {
    $response = \Pest\Laravel\post('/analyze', [
        'job_text' => str_repeat('A', 31),
        'cv_text' => str_repeat('B', 31),
    ]);
    $response->assertStatus(200);
    $response->assertViewIs('result');
    $response->assertViewHas('job_text', str_repeat('A', 31));
    $response->assertViewHas('cv_text', str_repeat('B', 31));
});


test('zeigt Fehler bei ungültiger KI-Antwort', function () {
    $mock = Mockery::mock(App\Ai\Agents\Analyzer::class);
    $mock->shouldReceive('prompt')->andReturn(new class () {
        public function toArray()
        {
            return ['foo' => 'bar'];
        }
    });
    app()->instance(App\Ai\Agents\Analyzer::class, $mock);
    $response = \Pest\Laravel\post('/analyze', [
        'job_text' => str_repeat('A', 31),
        'cv_text' => str_repeat('B', 31),
    ]);
    $response->assertStatus(200);
    $response->assertViewIs('result');
    $response->assertViewHas('error');
    $errorText = $response->viewData('error');
    expect($errorText)->toContain('AI-Analyse fehlgeschlagen');
    // Teste nur auf generischen Fehlertext, da die KI-Fehlermeldung dynamisch ist
});


test('zeigt Fehler bei Exception (z.B. Timeout)', function () {
    $mock = Mockery::mock(App\Ai\Agents\Analyzer::class);
    $mock->shouldReceive('prompt')->andThrow(new Exception('Timeout!'));
    app()->instance(App\Ai\Agents\Analyzer::class, $mock);
    $response = \Pest\Laravel\post('/analyze', [
        'job_text' => str_repeat('A', 31),
        'cv_text' => str_repeat('B', 31),
    ]);
    $response->assertStatus(200);
    $response->assertViewIs('result');
    $response->assertViewHas('error');
    $errorText = $response->viewData('error');
    expect($errorText)->toContain('AI-Analyse fehlgeschlagen');
    // Teste nur auf generischen Fehlertext, da die Exception-Meldung dynamisch ist
});
