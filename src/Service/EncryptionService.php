<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EncryptionService
{
    private const CIPHER_ALGO = "AES-128-CBC";
    private const ALGO = "sha256";

    private string $encryptionKey;

    public function __construct(
        private ParameterBagInterface $params
    ) {
        $this->encryptionKey = $this->params->get('app.encryption.key');
    }

    public function encrypt(string $plaintext): string
    {
        $ivlen = openssl_cipher_iv_length(self::CIPHER_ALGO);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, self::CIPHER_ALGO, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac(self::ALGO, $ciphertext_raw, $this->encryptionKey, true);

        return base64_encode($iv.$hmac.$ciphertext_raw);
    }

    public function decrypt(string $ciphertext): ?string
    {
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length(self::CIPHER_ALGO);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, self::CIPHER_ALGO, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac(self::ALGO, $ciphertext_raw, $this->encryptionKey, true);
        
        return hash_equals($hmac, $calcmac) ? $original_plaintext : null;
    }
}