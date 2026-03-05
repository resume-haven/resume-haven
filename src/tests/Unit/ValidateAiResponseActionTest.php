<?php

declare(strict_types=1);

use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;

describe('ValidateAiResponseAction', function () {
    test('akzeptiert valides JSON-Objekt', function () {
        $action = new ValidateAiResponseAction();
        $response = json_encode(['key' => 'value']);

        expect(fn () => $action->execute($response))->not()->toThrow(\Exception::class);
    });

    test('lehnt Response ab, die zu lang ist', function () {
        $action = new ValidateAiResponseAction();
        $response = json_encode(str_repeat('a', 1_000_001));

        expect(fn () => $action->execute($response))
            ->toThrow(\RuntimeException::class);
    });

    test('lehnt Response ab, die kein JSON-Objekt ist', function () {
        $action = new ValidateAiResponseAction();

        expect(fn () => $action->execute('["array"]'))
            ->toThrow(\RuntimeException::class);

        expect(fn () => $action->execute('"string"'))
            ->toThrow(\RuntimeException::class);
    });

    test('lehnt Response mit verdächtigen Patterns ab', function () {
        $action = new ValidateAiResponseAction();
        $response = json_encode(['eval' => 'eval()']);

        expect(fn () => $action->execute($response))
            ->toThrow(\RuntimeException::class);
    });
});
