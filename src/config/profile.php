<?php

declare(strict_types=1);

return [
    // Retention for anonymously stored resumes.
    // Stored CVs older than this threshold can no longer be loaded.
    'resume_retention_hours' => (static function (): int {
        $value = env('PROFILE_RESUME_RETENTION_HOURS', 168);

        if (! is_numeric($value)) {
            return 168;
        }

        return max((int) $value, 1);
    })(),
];
