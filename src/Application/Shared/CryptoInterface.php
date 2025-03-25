<?php

declare(strict_types = 1);

namespace App\Application\Shared;

interface CryptoInterface
{
    public const CIPHER_ALGO = "AES-128-CBC";
    public const ALGO = "sha256";

    public function encrypt(string $plainText): string;
    public function decrypt(string $cipherText): ?string;
}