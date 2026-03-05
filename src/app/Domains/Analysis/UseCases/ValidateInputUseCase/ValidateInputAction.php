<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ValidateInputUseCase;

use Illuminate\Support\Facades\Log;

/**
 * Action zur Validierung von Eingaben gegen Sicherheitsrisiken.
 *
 * Verantwortlichkeiten:
 * - Längenbeschränkung durchsetzen (max 50KB)
 * - Verdächtige Patterns erkennen (SQL, Script-Tags)
 * - Input-Bereinigung durchführen
 * - Security-Warnungen loggen
 */
class ValidateInputAction
{
    // Konfigurierbare Sicherheitslimits
    private const MAX_LENGTH_BYTES = 50 * 1024; // 50KB

    public function __construct(
        private PatternDetectorService $patternDetector,
        private InputSanitizerService $inputSanitizer,
    ) {}

    /**
     * Validiert Input gegen Sicherheitsrisiken.
     *
     * @param  string            $input     Zu validierender Text
     * @param  string            $fieldName Name des Feldes (fuer Logging)
     * @return ValidatedInputDto Validierter Input mit Metadaten
     *
     * @throws InputValidationException Bei kritischen Validierungsfehlern
     */
    public function execute(string $input, string $fieldName = 'input'): ValidatedInputDto
    {
        $this->validateLength($input, $fieldName);

        $suspiciousPatterns = $this->patternDetector->detect($input);
        $this->logSuspiciousPatterns($fieldName, $input, $suspiciousPatterns);

        $sanitized = $this->inputSanitizer->sanitize($input);
        $this->validateNotEmpty($sanitized);

        return new ValidatedInputDto(
            originalInput: $input,
            sanitizedInput: $sanitized,
            lengthBytes: strlen($sanitized),
            hasSuspiciousPatterns: $suspiciousPatterns !== [],
            suspiciousPatterns: $suspiciousPatterns,
        );
    }

    private function validateLength(string $input, string $fieldName): void
    {
        if (strlen($input) <= self::MAX_LENGTH_BYTES) {
            return;
        }

        Log::warning('Input exceeds maximum length', [
            'field' => $fieldName,
            'max_bytes' => self::MAX_LENGTH_BYTES,
            'actual_bytes' => strlen($input),
        ]);

        throw new InputValidationException('Der Eingabetext ist zu lang. Maximum: 50 KB');
    }

    /**
     * @param array<int, string> $suspiciousPatterns
     */
    private function logSuspiciousPatterns(string $fieldName, string $input, array $suspiciousPatterns): void
    {
        if ($suspiciousPatterns === []) {
            return;
        }

        Log::warning('Suspicious patterns detected in input', [
            'field' => $fieldName,
            'patterns' => $suspiciousPatterns,
            'length_bytes' => strlen($input),
        ]);
    }

    private function validateNotEmpty(string $sanitized): void
    {
        if (! empty(trim($sanitized))) {
            return;
        }

        throw new InputValidationException('Die Eingabe darf nicht leer sein');
    }
}
