<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use App\Application\Shared\CryptoInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class Crypto implements CryptoInterface 
{
    private readonly string $encryptionKey;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {
        $this->encryptionKey = $this->parameterBag->get('app.encryption.key');
    }

    public function decrypt(string $cipherText): ?string
    {
        $c = base64_decode($cipherText);
        $ivlen = openssl_cipher_iv_length(self::CIPHER_ALGO);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, self::CIPHER_ALGO, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac(self::ALGO, $ciphertext_raw, $this->encryptionKey, true);
        
        return hash_equals($hmac, $calcmac) ? $original_plaintext : null;
    }

    public function encrypt(string $plainText): string
    {
        $ivlen = openssl_cipher_iv_length(self::CIPHER_ALGO);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plainText, self::CIPHER_ALGO, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac(self::ALGO, $ciphertext_raw, $this->encryptionKey, true);

        return base64_encode($iv.$hmac.$ciphertext_raw);
    }
}