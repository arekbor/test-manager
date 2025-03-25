<?php 

declare(strict_types=1);

namespace App\Tests\AppSetting;

use App\Application\AppSetting\Service\AppSettingDecoder;
use App\Domain\Model\MailSmtpAppSetting;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class AppSettingDecoderTest extends TestCase
{
    private AppSettingDecoder $appSettingDecoder;

    protected function setUp(): void
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $this->appSettingDecoder = new AppSettingDecoder($serializer);
    }

    public function test_decodes_correctly(): void
    {
        //Arrange
        $mailSmtpAppSetting = new MailSmtpAppSetting(
            host: 'gmail.smtp.com',
            port: '546',
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
        $this->assertEquals('546', $decodedData['port']);
        $this->assertEquals('test@gmail.com', $decodedData['fromAddress']);
        $this->assertEquals('username@gmail.com', $decodedData['username']);
        $this->assertEquals('secret', $decodedData['password']);
        $this->assertEquals(true, $decodedData['smtpAuth']);
        $this->assertEquals('ssl', $decodedData['smtpSecure']);
        $this->assertEquals(10, $decodedData['timeout']);
    }

    public function test_encodes_correctly(): void
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