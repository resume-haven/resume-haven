<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\DecryptResumeAction;
use App\Domains\Profile\Actions\EncryptResumeAction;

it('verschluesselt und entschluesselt CV mit gleichem Token', function () {
    $encrypt = new EncryptResumeAction();
    $decrypt = new DecryptResumeAction();

    $token = 'test-token-123';
    $plainText = 'Mein CV Inhalt mit Erfahrung in Laravel und PHP.';

    $encrypted = $encrypt->execute($plainText, $token);
    $decrypted = $decrypt->execute($encrypted, $token);

    expect($encrypted)->not()->toBe($plainText);
    expect($decrypted)->toBe($plainText);
});

it('liefert null bei Entschluesselung mit falschem Token', function () {
    $encrypt = new EncryptResumeAction();
    $decrypt = new DecryptResumeAction();

    $encrypted = $encrypt->execute('Nur korrekt mit richtigem Token lesbar.', 'token-a');
    $decrypted = $decrypt->execute($encrypted, 'token-b');

    expect($decrypted)->toBeNull();
});

it('DecryptResumeAction gibt null bei ungueltigem Base64 zurueck', function () {
    $decrypt = new DecryptResumeAction();

    // Kein gueltiges Base64 → base64_decode liefert false
    $result = $decrypt->execute('!!!nicht-base64!!!', 'token');

    expect($result)->toBeNull();
});

it('DecryptResumeAction gibt null bei leerem Payload zurueck', function () {
    $decrypt = new DecryptResumeAction();

    // base64 von leerem String
    $result = $decrypt->execute(base64_encode(''), 'token');

    expect($result)->toBeNull();
});

it('DecryptResumeAction gibt null bei ungueltigem JSON zurueck', function () {
    $decrypt = new DecryptResumeAction();

    $result = $decrypt->execute(base64_encode('kein-json'), 'token');

    expect($result)->toBeNull();
});

it('DecryptResumeAction gibt null wenn JSON kein Array ist', function () {
    $decrypt = new DecryptResumeAction();

    $result = $decrypt->execute(base64_encode('"nur-ein-string"'), 'token');

    expect($result)->toBeNull();
});

it('DecryptResumeAction gibt null wenn iv/tag/cipher fehlen', function () {
    $decrypt = new DecryptResumeAction();

    $payload = base64_encode(json_encode(['iv' => 'abc'], JSON_THROW_ON_ERROR));
    $result = $decrypt->execute($payload, 'token');

    expect($result)->toBeNull();
});

it('DecryptResumeAction gibt null wenn iv-Base64 ungueltig ist', function () {
    $decrypt = new DecryptResumeAction();

    $payload = base64_encode(json_encode([
        'iv' => '!!!', // kein gueltiges Base64
        'tag' => base64_encode('tag'),
        'cipher' => base64_encode('cipher'),
    ], JSON_THROW_ON_ERROR));

    $result = $decrypt->execute($payload, 'token');

    expect($result)->toBeNull();
});

it('EncryptResumeAction erzeugt Base64-kodierten JSON-Payload', function () {
    $encrypt = new EncryptResumeAction();

    $encrypted = $encrypt->execute('Mein Lebenslauf', 'ein-token');

    $decoded = base64_decode($encrypted, true);
    expect($decoded)->not()->toBeFalse();

    $json = json_decode((string) $decoded, true);
    expect($json)->toBeArray();
    expect($json)->toHaveKeys(['iv', 'tag', 'cipher']);
});
