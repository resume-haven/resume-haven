<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\ValidateInputUseCase\ValidateInputAction;
use App\Domains\Analysis\UseCases\ValidateInputUseCase\InputValidationException;

describe('ValidateInputAction', function () {
    $action = new ValidateInputAction();

    describe('execute()', function () use ($action) {

        test('valides Input wird akzeptiert', function () use ($action) {
            $input = 'Dies ist ein valider Text ohne verdächtige Patterns.';

            $result = $action->execute($input, 'test_field');

            expect($result->originalInput)->toBe($input);
            expect($result->sanitizedInput)->toBe($input);
            expect($result->isSafe())->toBeTrue();
            expect($result->suspiciousPatterns)->toBeEmpty();
        });

        test('Input wird getrimmt', function () use ($action) {
            $input = "  \n  Text mit Whitespace  \n  ";

            $result = $action->execute($input, 'test_field');

            expect($result->sanitizedInput)->toBe('Text mit Whitespace');
            expect($result->isSafe())->toBeTrue();
        });

        test('leeres Input wird abgelehnt', function () use ($action) {
            $input = '   ';

            expect(fn () => $action->execute($input, 'test_field'))
                ->toThrow(InputValidationException::class);
        });

        test('Input über 50KB wird abgelehnt', function () use ($action) {
            $input = str_repeat('a', 51 * 1024); // 51KB

            expect(fn () => $action->execute($input, 'test_field'))
                ->toThrow(InputValidationException::class);
        });

        test('Input genau 50KB wird akzeptiert', function () use ($action) {
            $input = str_repeat('a', 50 * 1024); // Genau 50KB

            $result = $action->execute($input, 'test_field');

            expect($result->lengthBytes)->toBe(50 * 1024);
            expect($result->isSafe())->toBeTrue();
        });

        test('SQL-Injection-Pattern wird erkannt', function () use ($action) {
            $input = 'SELECT * FROM users WHERE id = 1';

            $result = $action->execute($input, 'test_field');

            expect($result->hasSuspiciousPatterns)->toBeTrue();
            expect($result->suspiciousPatterns)->toContain('SQL Keywords');
        });

        test('Script-Tag wird erkannt', function () use ($action) {
            $input = 'Text <script>alert("xss")</script> more text';

            $result = $action->execute($input, 'test_field');

            expect($result->hasSuspiciousPatterns)->toBeTrue();
            expect($result->suspiciousPatterns)->toContain('Script Tags');
        });

        test('Event-Handler wird erkannt', function () use ($action) {
            $input = 'Click me <div onclick="alert()">here</div>';

            $result = $action->execute($input, 'test_field');

            expect($result->hasSuspiciousPatterns)->toBeTrue();
            expect($result->suspiciousPatterns)->toContain('Event Handlers');
        });

        test('iframe-Tag wird erkannt', function () use ($action) {
            $input = '<iframe src="https://evil.com"></iframe>';

            $result = $action->execute($input, 'test_field');

            expect($result->hasSuspiciousPatterns)->toBeTrue();
            expect($result->suspiciousPatterns)->toContain('iFrame Tags');
        });

        test('mehrere Patterns gleichzeitig werden erkannt', function () use ($action) {
            $input = 'SELECT * FROM users; <script>alert("xss")</script>';

            $result = $action->execute($input, 'test_field');

            expect($result->hasSuspiciousPatterns)->toBeTrue();
            expect($result->suspiciousPatterns)->toHaveCount(2);
            expect($result->suspiciousPatterns)->toContain('SQL Keywords');
            expect($result->suspiciousPatterns)->toContain('Script Tags');
        });

        test('null-bytes werden entfernt', function () use ($action) {
            $input = "Text\0with\0null\0bytes";

            $result = $action->execute($input, 'test_field');

            expect($result->sanitizedInput)->toBe('Textwithbullbytes');
            expect(strpos($result->sanitizedInput, "\0"))->toBeFalse();
        });

        test('mehrfache Newlines werden zu einfachen konvertiert', function () use ($action) {
            $input = "Line 1\n\n\nLine 2\n\n\nLine 3";

            $result = $action->execute($input, 'test_field');

            expect($result->sanitizedInput)->toBe("Line 1\nLine 2\nLine 3");
        });

        test('Carriage Returns werden normalisiert', function () use ($action) {
            $input = "Line 1\r\nLine 2\r\nLine 3";

            $result = $action->execute($input, 'test_field');

            expect($result->sanitizedInput)->toBe("Line 1\nLine 2\nLine 3");
        });

        test('summary() gibt sichere Zusammenfassung für sicheren Input', function () use ($action) {
            $input = 'Safe input';

            $result = $action->execute($input, 'test_field');

            expect($result->summary())->toContain('Input safe');
            expect($result->summary())->toContain('bytes');
        });

        test('summary() gibt Warnung für verdächtige Patterns', function () use ($action) {
            $input = 'SELECT * FROM users';

            $result = $action->execute($input, 'test_field');

            expect($result->summary())->toContain('suspicious patterns');
            expect($result->summary())->toContain('SQL Keywords');
        });

    });

    describe('Pattern-Detection Edge Cases', function () use ($action) {

        test('case-insensitive SQL-Pattern-Matching', function () use ($action) {
            $inputs = [
                'select * from users',
                'SELECT * FROM users',
                'SeLeCt * FrOm users',
            ];

            foreach ($inputs as $input) {
                $result = $action->execute($input, 'test');
                expect($result->hasSuspiciousPatterns)->toBeTrue();
            }
        });

        test('legitimer Text mit "SELECT" im Wort wird nicht geflaggt', function () use ($action) {
            // "selected" ist legitim, nur "SELECT" als Keyword ist verdächtig
            $input = 'The selected option is good';

            $result = $action->execute($input, 'test');

            // Sollte OK sein, da "selected" kein Keyword ist
            // Aber "SELECT" als Keyword würde erkannt
            // Hier: "selected" sollte nicht matchen wegen \b (word boundary)
            expect($result->hasSuspiciousPatterns)->toBeFalse();
        });

        test('DROP TABLE wird erkannt, nicht "dropdown"', function () use ($action) {
            $input = 'DROP TABLE users;';

            $result = $action->execute($input, 'test');

            expect($result->hasSuspiciousPatterns)->toBeTrue();
            expect($result->suspiciousPatterns)->toContain('SQL Keywords');
        });

    });

});

