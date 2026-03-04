<?php

declare(strict_types=1);

use App\Services\AnalysisCacheService;
use App\Dto\AnalyzeRequestDto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('AnalysisCacheService', function () {
    it('speichert und findet Einträge mit Tags', function () {
        $service = new AnalysisCacheService();
        $dto = new AnalyzeRequestDto('UnitTest Job', 'UnitTest CV');
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
        expect($found['tags'])->not()->toBeNull();
    });

    it('gibt null zurück, wenn kein Eintrag existiert (Hash)', function () {
        $service = new AnalysisCacheService();
        $dto = new AnalyzeRequestDto('Nicht vorhandener Job', 'Nicht vorhandener CV');
        $found = $service->getByDto($dto);
        expect($found)->toBeNull();
    });
});
