<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ValidateInputUseCase;

use Exception;

/**
 * Exception für Input-Validierungsfehler.
 */
final class InputValidationException extends Exception
{
    public function __construct(string $message = 'Input validation failed', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
