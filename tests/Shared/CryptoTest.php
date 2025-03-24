<?php 

declare(strict_types=1);

namespace App\Tests\Shared;

use App\Infrastructure\Shared\Crypto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class CryptoTest extends TestCase
{
    private readonly Crypto $crypto;

    protected function setUp(): void
    {
        $parameterBag = new ParameterBag([
            "app.encryption.key" => "5D7HvoJinRlmlPaRHa5RMt2HuSSkaagO"
        ]);

        $this->crypto = new Crypto($parameterBag);
    }

    public function test_encrypts_plainText_correctly(): void
    {
        //Arrange
        $plainText = "Test text to be encrypted.";

        //Act
        $encryptedData = $this->crypto->encrypt($plainText);

        $anotherEncryptedData = $this->crypto->encrypt($plainText);

        //Assert
        $this->assertNotEmpty($encryptedData);
        $this->assertNotEquals("Test text to be encrypted.", $encryptedData);
        $this->assertStringNotContainsString($plainText, $encryptedData);
        $this->assertNotEquals($encryptedData, $anotherEncryptedData);
    }

    public function test_decrypts_correctly(): void
    {
        //Arrange
        $plainText = "Test text to be encrypted.";
        
        //Act
        $encryptedData = $this->crypto->encrypt($plainText);
        $decryptedData = $this->crypto->decrypt($encryptedData);

        //Assert
        $this->assertEquals($plainText, $decryptedData);
    }
}