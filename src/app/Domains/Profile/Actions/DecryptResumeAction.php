<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

class DecryptResumeAction
{
    public function execute(string $encryptedPayload, string $token): ?string
    {
        try {
            $decoded = base64_decode($encryptedPayload, true);

            if (! is_string($decoded) || $decoded === '') {
                return null;
            }

            /** @var mixed $payload */
            $payload = json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($payload)) {
                return null;
            }

            $iv = $payload['iv'] ?? null;
            $tag = $payload['tag'] ?? null;
            $cipher = $payload['cipher'] ?? null;

            if (! is_string($iv) || ! is_string($tag) || ! is_string($cipher)) {
                return null;
            }

            $ivRaw = base64_decode($iv, true);
            $tagRaw = base64_decode($tag, true);
            $cipherRaw = base64_decode($cipher, true);

            if (! is_string($ivRaw) || ! is_string($tagRaw) || ! is_string($cipherRaw)) {
                return null;
            }

            $key = hash('sha256', $token, true);
            $plainText = openssl_decrypt($cipherRaw, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $ivRaw, $tagRaw);

            return is_string($plainText) ? $plainText : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
