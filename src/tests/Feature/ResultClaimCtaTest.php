<?php

declare(strict_types=1);

use App\Dto\AnalyzeResultDto;
use App\Models\User;
use App\Services\AnalyzeApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('zeigt claim-cta fuer gaeste mit resume_token in session', function (): void {
    $mock = Mockery::mock(AnalyzeApplicationService::class);
    $mock->shouldReceive('analyze')->once()->andReturn(new AnalyzeResultDto(
        str_repeat('A', 31),
        str_repeat('B', 31),
        ['PHP'],
        ['Laravel'],
        [['requirement' => 'PHP', 'experience' => 'Laravel']],
        [],
        null,
        ['matches' => [], 'gaps' => []],
        [],
    ));
    app()->instance(AnalyzeApplicationService::class, $mock);

    $response = $this->withSession([
        'resume_token' => str_repeat('T', 32),
        'resume_claimed' => false,
    ])->post('/analyze', [
        'job_text' => str_repeat('A', 31),
        'cv_text' => str_repeat('B', 31),
    ]);

    $response->assertStatus(200);
    $response->assertSee('CV sichern &amp; Konto erstellen', false);
    $response->assertDontSee('CV deinem Konto zugeordnet');
});

test('zeigt kein claim-cta fuer eingeloggte nutzende', function (): void {
    $user = User::factory()->create();

    $mock = Mockery::mock(AnalyzeApplicationService::class);
    $mock->shouldReceive('analyze')->once()->andReturn(new AnalyzeResultDto(
        str_repeat('A', 31),
        str_repeat('B', 31),
        ['PHP'],
        ['Laravel'],
        [['requirement' => 'PHP', 'experience' => 'Laravel']],
        [],
        null,
        ['matches' => [], 'gaps' => []],
        [],
    ));
    app()->instance(AnalyzeApplicationService::class, $mock);

    $response = $this->actingAs($user)->withSession([
        'resume_token' => str_repeat('T', 32),
        'resume_claimed' => false,
    ])->post('/analyze', [
        'job_text' => str_repeat('A', 31),
        'cv_text' => str_repeat('B', 31),
    ]);

    $response->assertStatus(200);
    $response->assertDontSee('CV sichern &amp; Konto erstellen', false);
    $response->assertDontSee('CV deinem Konto zugeordnet');
});

test('zeigt success-hinweis wenn resume bereits geclaimt ist', function (): void {
    $user = User::factory()->create();

    $mock = Mockery::mock(AnalyzeApplicationService::class);
    $mock->shouldReceive('analyze')->once()->andReturn(new AnalyzeResultDto(
        str_repeat('A', 31),
        str_repeat('B', 31),
        ['PHP'],
        ['Laravel'],
        [['requirement' => 'PHP', 'experience' => 'Laravel']],
        [],
        null,
        ['matches' => [], 'gaps' => []],
        [],
    ));
    app()->instance(AnalyzeApplicationService::class, $mock);

    $response = $this->actingAs($user)->withSession([
        'resume_token' => str_repeat('T', 32),
        'resume_claimed' => true,
    ])->post('/analyze', [
        'job_text' => str_repeat('A', 31),
        'cv_text' => str_repeat('B', 31),
    ]);

    $response->assertStatus(200);
    $response->assertSee('CV deinem Konto zugeordnet');
    $response->assertDontSee('CV sichern &amp; Konto erstellen', false);
});
