<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions {
    function openssl_encrypt(
        string $data,
        string $cipherAlgo,
        string $passphrase,
        int $options,
        string $iv,
        &$tag = null,
        string $aad = '',
        int $tagLength = 16,
    ): string|false {
        if (($GLOBALS['encrypt_resume_force_failure'] ?? false) === true) {
            return false;
        }

        return \openssl_encrypt($data, $cipherAlgo, $passphrase, $options, $iv, $tag, $aad, $tagLength);
    }
}

namespace {
    use App\Domains\Profile\Actions\EncryptResumeAction;

    beforeEach(function (): void {
        $GLOBALS['encrypt_resume_force_failure'] = false;
    });

    afterEach(function (): void {
        unset($GLOBALS['encrypt_resume_force_failure']);
    });

    it('erzeugt einen base64-kodierten json payload im erfolgsfall', function (): void {
        $encrypted = (new EncryptResumeAction())->execute('Mein Lebenslauf', 'ein-token');

        $decoded = base64_decode($encrypted, true);

        expect($decoded)->not->toBeFalse();
        expect(json_decode((string) $decoded, true))
            ->toBeArray()
            ->toHaveKeys(['iv', 'tag', 'cipher']);
    });

    it('wirft eine RuntimeException wenn openssl encryption fehlschlaegt', function (): void {
        $GLOBALS['encrypt_resume_force_failure'] = true;

        expect(fn () => (new EncryptResumeAction())->execute('Mein Lebenslauf', 'ein-token'))
            ->toThrow(RuntimeException::class, 'CV encryption failed.');
    });
}
