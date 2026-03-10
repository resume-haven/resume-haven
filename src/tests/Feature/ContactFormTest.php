<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('Kontakt-Seite ist erreichbar', function () {
    $response = $this->get(route('contact.show'));

    $response->assertStatus(200);
    $response->assertViewIs('legal.kontakt');
});

test('Kontaktformular wird erfolgreich abgeschickt', function () {
    Log::spy();

    $response = $this->post(route('contact.submit'), [
        'name' => 'Max Mustermann',
        'email' => 'max@example.com',
        'message' => 'Das ist eine Testnachricht mit ausreichend Text.',
    ]);

    $response->assertRedirect(route('contact.show'));
    $response->assertSessionHas('success');

    Log::shouldHaveReceived('info')
        ->once()
        ->with('Kontaktformular-Eingang', \Mockery::type('array'));
});

test('Kontaktformular schlaegt bei fehlenden Pflichtfeldern fehl', function () {
    $response = $this->post(route('contact.submit'), []);

    $response->assertSessionHasErrors(['name', 'email', 'message']);
});

test('Kontaktformular schlaegt bei zu kurzem Namen fehl', function () {
    $response = $this->post(route('contact.submit'), [
        'name' => 'A',
        'email' => 'test@example.com',
        'message' => str_repeat('Text ', 5),
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('Kontaktformular schlaegt bei ungueltigem E-Mail-Format fehl', function () {
    $response = $this->post(route('contact.submit'), [
        'name' => 'Max Mustermann',
        'email' => 'kein-email',
        'message' => str_repeat('Text ', 5),
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('Kontaktformular schlaegt bei zu kurzer Nachricht fehl', function () {
    $response = $this->post(route('contact.submit'), [
        'name' => 'Max Mustermann',
        'email' => 'max@example.com',
        'message' => 'Kurz',
    ]);

    $response->assertSessionHasErrors(['message']);
});
