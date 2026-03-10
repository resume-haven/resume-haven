<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

class EncryptResumeAction
{
    public function execute(string $plainText, string $token): string
    {
        $key = hash('sha256', $token, true);
        $iv = random_bytes(12);

        $cipherText = openssl_encrypt($plainText, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($cipherText === false) {
            throw new \RuntimeException('CV encryption failed.');
        }

        $json = json_encode([
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'cipher' => base64_encode($cipherText),
        ], JSON_THROW_ON_ERROR);

        return base64_encode($json);
    }
}
