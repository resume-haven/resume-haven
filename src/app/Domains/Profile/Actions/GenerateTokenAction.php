<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

class GenerateTokenAction
{
    public function execute(): string
    {
        $bytes = random_bytes(32);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
