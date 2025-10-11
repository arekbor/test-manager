<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class AppSettingDecoderTest extends TestCase
{
    private AppSettingDecoder $appSettingDecoder;

    protected function setUp(): void
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $this->appSettingDecoder = new AppSettingDecoder($serializer);
    }

    #[Test]
    public function testDecodesCorrectly(): void
    {
        //Arrange
        $mailSmtpAppSetting = new MailSmtpAppSetting(
            host: 'gmail.smtp.com',
            port: 546,
            fromAddress: 'test@gmail.com',
            username: 'username@gmail.com',
            password: 'secret',
            smtpAuth: true,
            smtpSecure: 'ssl',
            timeout: 10
        );

        //Act
        $decodedData = $this->appSettingDecoder->decode($mailSmtpAppSetting);

        //Assert
        $this->assertEquals('gmail.smtp.com', $decodedData['host']);
        $this->assertEquals(546, $decodedData['port']);
        $this->assertEquals('test@gmail.com', $decodedData['fromAddress']);
        $this->assertEquals('username@gmail.com', $decodedData['username']);
        $this->assertEquals('secret', $decodedData['password']);
        $this->assertEquals(true, $decodedData['smtpAuth']);
        $this->assertEquals('ssl', $decodedData['smtpSecure']);
        $this->assertEquals(10, $decodedData['timeout']);
    }

    #[Test]
    public function testEncodesCorrectly(): void
    {
        //Arrange
        $mailSmtpAppSetting = [
            'host' => 'gmail.smtp.com',
            'port' => '546',
            'fromAddress' => 'test@gmail.com',
            'username' => 'username@gmail.com',
            'password' => 'secret',
            'smtpAuth' => true,
            'smtpSecure' => 'ssl',
            'timeout' => 10
        ];

        //Act
        /**
         * @var MailSmtpAppSetting $encodedData
         */
        $encodedData = $this->appSettingDecoder->encode($mailSmtpAppSetting, MailSmtpAppSetting::class);

        //Assert
        $this->assertEquals('gmail.smtp.com', $encodedData->getHost());
        $this->assertEquals('546', $encodedData->getPort());
        $this->assertEquals('test@gmail.com', $encodedData->getFromAddress());
        $this->assertEquals('username@gmail.com', $encodedData->getUsername());
        $this->assertEquals('secret', $encodedData->getPassword());
        $this->assertEquals(true, $encodedData->getSmtpAuth());
        $this->assertEquals('ssl', $encodedData->getSmtpSecure());
        $this->assertEquals(10, $encodedData->getTimeout());
    }
}
