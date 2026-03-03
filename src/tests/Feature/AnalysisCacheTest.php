<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Services\AnalysisCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('AnalysisCacheService speichert und findet Einträge mit Tags', function () {
    $service = new AnalysisCacheService();
    $dto = new AnalyzeRequestDto('Test Job', 'Test CV');
    $result = [
        'requirements' => ['foo'],
        'experiences' => ['bar'],
        'matches' => [],
        'gaps' => [],
        'tags' => [
            'matches' => [],
            'gaps' => [],
        ],
    ];
    $service->putByDto($dto, $result);
    $found = $service->getByDto($dto);
    expect($found)->toBe($result);
    expect($found['tags'])->toHaveKey('matches');
    expect($found['tags'])->toHaveKey('gaps');
});

test('AnalysisCacheService gibt null zurück, wenn kein Eintrag existiert', function () {
    $service = new AnalysisCacheService();
    $dto = new AnalyzeRequestDto('Nicht vorhandener Job', 'Nicht vorhandener CV');
    $found = $service->getByDto($dto);
    expect($found)->toBeNull();
});
