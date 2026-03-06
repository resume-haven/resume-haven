<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AnalyzeApplicationService;

describe('AnalyzeApplicationService', function () {
    test('delegiert analyze an AiAnalyzerInterface', function () {
        $dto = new AnalyzeRequestDto('job', 'cv');
        $expected = new AnalyzeResultDto('job', 'cv', [], [], [], [], null, null);

        $analyzer = Mockery::mock(AiAnalyzerInterface::class);
        $analyzer->shouldReceive('analyze')->once()->with($dto)->andReturn($expected);

        $service = new AnalyzeApplicationService($analyzer);
        $actual = $service->analyze($dto);

        expect($actual)->toBe($expected);
    });

    test('delegiert isAvailable und getProviderName', function () {
        $analyzer = Mockery::mock(AiAnalyzerInterface::class);
        $analyzer->shouldReceive('isAvailable')->once()->andReturnTrue();
        $analyzer->shouldReceive('getProviderName')->once()->andReturn('mock');

        $service = new AnalyzeApplicationService($analyzer);

        expect($service->isAvailable())->toBeTrue();
        expect($service->getProviderName())->toBe('mock');
    });
});

