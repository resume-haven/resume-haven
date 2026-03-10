<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('zeigt CV-Speicher und Lade-Bedienelemente auf analyze an', function () {
    $response = $this->get(route('analyze'));

    $response->assertStatus(200);
    $response->assertSee('CV speichern');
    $response->assertSee('Lebenslauf laden');
    $response->assertSee('Resume-Token eingeben');
});

it('zeigt Speicher-Link mit Copy-Button nach erfolgreicher Speicherung', function () {
    $cv = str_repeat('Erfahrung mit Laravel und PHP. ', 3);

    $response = $this->post(route('profile.store'), ['cv_text' => $cv]);
    $token = $response->getSession()->get('resume_token');
    $link = $response->getSession()->get('resume_link');

    expect($token)->not()->toBeEmpty();
    expect($link)->toContain('/profile/load/');

    // Folgeaufruf: Session-Daten in der View sichtbar?
    $viewResponse = $this->withSession([
        'resume_token' => $token,
        'resume_link' => $link,
        'success' => 'Lebenslauf gespeichert.',
    ])->get(route('analyze'));

    $viewResponse->assertSee('resume_link_display', false);
    $viewResponse->assertSee('copy_link_button', false);
    $viewResponse->assertSee('Speichere diesen Link');
    $viewResponse->assertSee((string) $link);
});

it('zeigt Hinweis wenn kein Speicher-Link in der Session ist', function () {
    $response = $this->get(route('analyze'));

    // Ohne Session-Daten darf der Speicher-Link-Hinweis nicht sichtbar sein
    $response->assertDontSee('Speichere diesen Link');
    $response->assertDontSee('Dein Speicher-Link:');
});
