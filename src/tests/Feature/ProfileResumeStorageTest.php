<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\EncryptResumeAction;
use App\Domains\Profile\Actions\GenerateTokenAction;
use App\Models\StoredResume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('speichert CV und erzeugt Token-Link', function () {
    $cv = str_repeat('Erfahrung mit Laravel und PHP. ', 3);

    $response = $this->post(route('profile.store'), [
        'cv_text' => $cv,
    ]);

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHas('resume_token');
    $response->assertSessionHas('resume_claimed', false);
    $response->assertSessionHas('resume_link');

    expect(StoredResume::query()->count())->toBe(1);
    $stored = StoredResume::query()->first();
    expect($stored)->not()->toBeNull();
    expect($stored?->encrypted_cv)->not()->toBe($cv);
    expect($stored?->user_id)->toBeNull();
});

it('speichert CV fuer eingeloggten nutzer direkt geclaimt', function () {
    $user = User::factory()->create();
    $cv = str_repeat('Laravel Erfahrung mit Teamlead und CI. ', 3);

    $response = $this->actingAs($user)->post(route('profile.store'), [
        'cv_text' => $cv,
    ]);

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHas('resume_claimed', true);

    $stored = StoredResume::query()->first();

    expect($stored)->not()->toBeNull();
    expect($stored?->user_id)->toBe($user->id);
});

it('validiert cv_text beim Speichern', function () {
    $response = $this->post(route('profile.store'), [
        'cv_text' => 'zu kurz',
    ]);

    $response->assertSessionHasErrors(['cv_text']);
});

it('laedt CV ueber Token und fuellt Session', function () {
    $token = (new GenerateTokenAction())->execute();
    $cv = 'Lang genuger CV Text fuer den Lade-Test mit Laravel Erfahrung.';
    $encrypted = (new EncryptResumeAction())->execute($cv, $token);

    StoredResume::query()->create([
        'token' => $token,
        'encrypted_cv' => $encrypted,
    ]);

    $response = $this->get(route('profile.load', ['token' => $token]));

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHas('loaded_cv', $cv);
    $response->assertSessionHas('loaded_token', $token);
    expect(StoredResume::query()->first()?->last_accessed_at)->not()->toBeNull();
});

it('gibt Fehler bei ungueltigem Token-Format zurueck', function () {
    $response = $this->get(route('profile.load', ['token' => '***invalid***']));

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHasErrors(['resume_token']);
});

it('gibt Fehler zurueck wenn Token nicht gefunden wird', function () {
    $response = $this->get(route('profile.load', ['token' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcd1234_-']));

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHasErrors(['resume_token']);
});

it('gibt Fehler zurueck wenn gespeicherter CV nicht entschluesselt werden kann', function () {
    $token = (new GenerateTokenAction())->execute();

    StoredResume::query()->create([
        'token' => $token,
        'encrypted_cv' => 'defekt',
    ]);

    $response = $this->get(route('profile.load', ['token' => $token]));

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHasErrors(['resume_token']);
});

it('gibt Fehler zurueck und entfernt den Datensatz wenn gespeicherter CV abgelaufen ist', function () {
    config(['profile.resume_retention_hours' => 24]);

    $token = (new GenerateTokenAction())->execute();
    $cv = 'Aelterer gespeicherter CV mit gueltiger Struktur aber abgelaufener Aufbewahrung.';
    $encrypted = (new EncryptResumeAction())->execute($cv, $token);

    DB::table('stored_resumes')->insert([
        'token' => $token,
        'encrypted_cv' => $encrypted,
        'last_accessed_at' => now()->subHours(26),
        'created_at' => now()->subHours(30),
        'updated_at' => now()->subHours(30),
    ]);

    expect(StoredResume::query()->count())->toBe(1);

    $response = $this->get(route('profile.load', ['token' => $token]));

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHasErrors(['resume_token']);
    expect(StoredResume::query()->count())->toBe(0);
});
