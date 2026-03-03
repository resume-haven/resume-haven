<?php

declare(strict_types=1);

use App\Models\AnalysisCache;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('cache:clear-analysis leert alle Einträge', function () {
    // Erstelle Test-Einträge
    AnalysisCache::create([
        'request_hash' => hash('sha256', 'test1'),
        'job_text' => 'Job 1',
        'cv_text' => 'CV 1',
        'result' => ['test' => 'data1'],
    ]);

    AnalysisCache::create([
        'request_hash' => hash('sha256', 'test2'),
        'job_text' => 'Job 2',
        'cv_text' => 'CV 2',
        'result' => ['test' => 'data2'],
    ]);

    expect(AnalysisCache::count())->toBe(2);

    // Führe Command aus
    $this->artisan('cache:clear-analysis')
        ->expectsOutput('✓ Cleared 2 cache entries.')
        ->assertExitCode(0);

    // Prüfe dass Cache leer ist
    expect(AnalysisCache::count())->toBe(0);
});

test('cache:clear-analysis gibt Nachricht aus wenn Cache leer ist', function () {
    expect(AnalysisCache::count())->toBe(0);

    $this->artisan('cache:clear-analysis')
        ->expectsOutput('✓ Cache table is already empty.')
        ->assertExitCode(0);
});

test('cache:clear-analysis --older-than=7 löscht nur alte Einträge', function () {
    // Erstelle alten Eintrag (vor 10 Tagen) mit DB::table um Timestamps zu setzen
    \DB::table('analysis_cache')->insert([
        'request_hash' => hash('sha256', 'old'),
        'job_text' => 'Old Job',
        'cv_text' => 'Old CV',
        'result' => json_encode(['test' => 'old']),
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(10),
    ]);

    // Erstelle neuen Eintrag (heute)
    AnalysisCache::create([
        'request_hash' => hash('sha256', 'new'),
        'job_text' => 'New Job',
        'cv_text' => 'New CV',
        'result' => ['test' => 'new'],
    ]);

    expect(AnalysisCache::count())->toBe(2);

    // Führe Command aus mit --older-than=7
    $this->artisan('cache:clear-analysis --older-than=7')
        ->expectsOutput('✓ Deleted 1 cache entry older than 7 days.')
        ->assertExitCode(0);

    // Prüfe dass nur neuer Eintrag vorhanden ist
    expect(AnalysisCache::count())->toBe(1);
    expect(AnalysisCache::first()->job_text)->toBe('New Job');
});

test('cache:clear-analysis lehnt negative --older-than ab', function () {
    $this->artisan('cache:clear-analysis --older-than=-5')
        ->expectsOutput('Error: --older-than must be a positive number.')
        ->assertExitCode(1);
});

test('cache:clear-analysis --older-than mit keinen alten Einträgen gibt Nachricht aus', function () {
    // Erstelle nur neuen Eintrag
    AnalysisCache::create([
        'request_hash' => hash('sha256', 'new'),
        'job_text' => 'New Job',
        'cv_text' => 'New CV',
        'result' => ['test' => 'new'],
    ]);

    $this->artisan('cache:clear-analysis --older-than=7')
        ->expectsOutput('✓ No cache entries older than 7 days found.')
        ->assertExitCode(0);

    // Eintrag sollte noch da sein
    expect(AnalysisCache::count())->toBe(1);
});
