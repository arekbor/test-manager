<?php 

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\Infrastructure\Shared\Crypto;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function testEncryptsPlainTextCorrectly(): void
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

    #[Test]
    public function testDecryptsCorrectly(): void
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