<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\ValidateInputUseCase\InputSanitizerService;

describe('InputSanitizerService', function () {
    test('entfernt Null-Bytes und trimmt Whitespace', function () {
        $service = new InputSanitizerService();

        $result = $service->sanitize("  a\0bc  ");

        expect($result)->toBe('abc');
    });

    test('normalisiert mehrfache Newlines und CRLF', function () {
        $service = new InputSanitizerService();

        $result = $service->sanitize("Line1\r\n\r\n\r\nLine2");

        expect($result)->toBe("Line1\nLine2");
    });
});
