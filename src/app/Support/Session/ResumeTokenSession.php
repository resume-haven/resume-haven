<?php

declare(strict_types=1);

namespace App\Support\Session;

use Illuminate\Contracts\Session\Session;

final class ResumeTokenSession
{
    public const CURRENT_TOKEN_KEY = 'resume_token';
    public const TOKENS_KEY = 'resume_tokens';

    /** @return list<string> */
    public function all(Session $session): array
    {
        $tokens = $session->get(self::TOKENS_KEY);
        $currentToken = $session->get(self::CURRENT_TOKEN_KEY);

        if (! is_array($tokens)) {
            $tokens = [];
        }

        if (is_string($currentToken) && $currentToken !== '') {
            $tokens[] = $currentToken;
        }

        return $this->normalize($tokens);
    }

    public function add(Session $session, string $token): void
    {
        $tokens = $this->all($session);
        $tokens[] = $token;
        $tokens = $this->normalize($tokens);

        $session->put(self::TOKENS_KEY, $tokens);
        $session->put(self::CURRENT_TOKEN_KEY, $token);
    }

    public function current(Session $session): ?string
    {
        $currentToken = $session->get(self::CURRENT_TOKEN_KEY);

        if (is_string($currentToken) && $currentToken !== '') {
            return $currentToken;
        }

        $tokens = $this->all($session);

        if ($tokens === []) {
            return null;
        }

        return $tokens[array_key_last($tokens)];
    }

    public function remove(Session $session, string $token): void
    {
        $tokens = array_values(array_filter(
            $this->all($session),
            static fn (string $existing): bool => $existing !== $token,
        ));

        if ($tokens === []) {
            $session->forget(self::TOKENS_KEY);
            $session->forget(self::CURRENT_TOKEN_KEY);

            return;
        }

        $session->put(self::TOKENS_KEY, $tokens);

        if ($session->get(self::CURRENT_TOKEN_KEY) === $token) {
            $session->put(self::CURRENT_TOKEN_KEY, $tokens[array_key_last($tokens)]);
        }
    }

    /** @return list<string> */
    public function normalize(mixed $tokens): array
    {
        if (! is_array($tokens)) {
            return [];
        }

        $normalized = [];

        foreach ($tokens as $token) {
            if (! is_string($token) || $token === '') {
                continue;
            }

            $normalized[$token] = $token;
        }

        return array_values($normalized);
    }
}
