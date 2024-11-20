<?php 

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\EncryptionService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class EncryptionServiceTest extends TestCase
{
    private EncryptionService $encryptionService;

    protected function setUp(): void
    {
        $params = new ParameterBag([
            "app.encryption.key" => "5D7HvoJinRlmlPaRHa5RMt2HuSSkaagO"
        ]);

        $this->encryptionService = new EncryptionService($params);
    }

    public function testEncryptAndDecrypt(): void
    {
        $plaintext = "Test text to be encrypted.";

        $encrypted = $this->encryptionService->encrypt($plaintext);
        $this->assertNotEmpty($encrypted, "The encrypted text should not be empty.");

        $decrypted = $this->encryptionService->decrypt($encrypted);
        $this->assertEquals($plaintext, $decrypted, "The deciphered text should be consistent with the original.");
    }
}