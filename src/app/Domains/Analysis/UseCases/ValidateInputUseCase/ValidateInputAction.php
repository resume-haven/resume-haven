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

    // Verdächtige Patterns (Case-Insensitive Regex)
    private const SUSPICIOUS_PATTERNS = [
        // SQL-Injection-Patterns
        '/\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION|ALTER|CREATE|EXECUTE|EXEC)\b/i',
        // JavaScript-Injection
        '/<script[^>]*>.*?<\/script>/is',
        '/on\w+\s*=/i', // Event-Handler (onclick=, onload=, etc.)
        // HTML-Injection
        '/<iframe/i',
        '/<object/i',
        '/<embed/i',
    ];

    /**
     * Validiert Input gegen Sicherheitsrisiken.
     *
     * @param  string            $input     Zu validierender Text
     * @param  string            $fieldName Name des Feldes (für Logging)
     * @return ValidatedInputDto Validierter Input mit Metadaten
     *
     * @throws InputValidationException Bei kritischen Validierungsfehlern
     */
    public function execute(string $input, string $fieldName = 'input'): ValidatedInputDto
    {
        // 1. Längenprüfung (hard limit)
        if (strlen($input) > self::MAX_LENGTH_BYTES) {
            Log::warning('Input exceeds maximum length', [
                'field' => $fieldName,
                'max_bytes' => self::MAX_LENGTH_BYTES,
                'actual_bytes' => strlen($input),
            ]);
            throw new InputValidationException(
                'Der Eingabetext ist zu lang. Maximum: 50 KB'
            );
        }

        // 2. Verdächtige Patterns prüfen (warning, nicht blocking)
        $suspiciousPatterns = $this->detectSuspiciousPatterns($input);
        if (! empty($suspiciousPatterns)) {
            Log::warning('Suspicious patterns detected in input', [
                'field' => $fieldName,
                'patterns' => $suspiciousPatterns,
                'length_bytes' => strlen($input),
            ]);
        }

        // 3. Input-Bereinigung (Whitespace trimmen, null-bytes entfernen)
        $sanitized = $this->sanitizeInput($input);

        // 4. Leere Input Prüfung
        if (empty(trim($sanitized))) {
            throw new InputValidationException(
                'Die Eingabe darf nicht leer sein'
            );
        }

        return new ValidatedInputDto(
            originalInput: $input,
            sanitizedInput: $sanitized,
            lengthBytes: strlen($sanitized),
            hasSuspiciousPatterns: ! empty($suspiciousPatterns),
            suspiciousPatterns: $suspiciousPatterns,
        );
    }

    /**
     * Erkennt verdächtige Patterns im Input.
     *
     * @return array<int, string> Array von erkannten Patterns
     */
    private function detectSuspiciousPatterns(string $input): array
    {
        $detected = [];

        foreach (self::SUSPICIOUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $input)) {
                // Pattern in lesbare Form konvertieren
                $readablePattern = $this->patternToReadable($pattern);
                $detected[] = $readablePattern;
            }
        }

        return array_unique($detected);
    }

    /**
     * Konvertiert Regex-Pattern in lesbare Beschreibung.
     */
    private function patternToReadable(string $pattern): string
    {
        return match ($pattern) {
            '/\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION|ALTER|CREATE|EXECUTE|EXEC)\b/i' => 'SQL Keywords',
            '/<script[^>]*>.*?<\/script>/is' => 'Script Tags',
            '/on\w+\s*=/i' => 'Event Handlers',
            '/<iframe/i' => 'iFrame Tags',
            '/<object/i' => 'Object Tags',
            '/<embed/i' => 'Embed Tags',
            default => 'Unknown Pattern',
        };
    }

    /**
     * Bereinigt den Input.
     */
    private function sanitizeInput(string $input): string
    {
        // 1. Null-Bytes entfernen (Security)
        $input = str_replace("\0", '', $input);

        // 2. Trim Whitespace (User Experience)
        $input = trim($input);

        // 3. Mehrfache Newlines zu Einfachen konvertieren
        $input = preg_replace('/\n\n+/', "\n", $input) ?? $input;

        // 4. Carriage Returns normalisieren (Windows <-> Unix)
        $input = str_replace("\r\n", "\n", $input);

        return $input;
    }
}
