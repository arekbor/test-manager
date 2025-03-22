<?php 

declare(strict_types=1);

namespace App\Tests\Service;

use App\Domain\Entity\AppSetting;
use App\Domain\Model\MailSmtpAppSetting;
use App\Service\AppSettingService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AppSettingServiceTest extends TestCase
{
    private AppSettingService $appSettingService;

    protected function setUp(): void
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $this->appSettingService = new AppSettingService($serializer);
    }

    #[DataProvider('mailSmtpAppSettingProvider')]
    public function testSetValue(MailSmtpAppSetting $mailSmtpAppSetting): void
    {
        $appSetting = $this
            ->appSettingService
            ->setValue($key = "mail.smtp", $mailSmtpAppSetting)
        ;

        $this->assertEquals($appSetting->getKey(), $key);

        $this->assertEquals($appSetting->getValue(), [
            "host" => $mailSmtpAppSetting->getHost(),
            "port" => $mailSmtpAppSetting->getPort(),
            "fromAddress" => $mailSmtpAppSetting->getFromAddress(),
            "username" => $mailSmtpAppSetting->getUsername(),
            "password" => $mailSmtpAppSetting->getPassword(),
            "smtpAuth" => $mailSmtpAppSetting->getSmtpAuth(),
            "smtpSecure" => $mailSmtpAppSetting->getSmtpSecure(),
            "timeout" => $mailSmtpAppSetting->getTimeout(),
        ]);
    }

    #[DataProvider('mailSmtpAppSettingProvider')]
    public function testGetValue(MailSmtpAppSetting $mailSmtpAppSetting): void
    {
        $appSetting = new AppSetting();
        $appSetting->setKey("mail_smtp_app_setting.key");
        $appSetting->setValue([
            "host" => $host = $mailSmtpAppSetting->getHost(),
            "port" => $port = $mailSmtpAppSetting->getPort(),
            "fromAddress" => $fromAddress = $mailSmtpAppSetting->getFromAddress(),
            "username" => $username = $mailSmtpAppSetting->getUsername(),
            "password" => $password = $mailSmtpAppSetting->getPassword(),
            "smtpAuth" => $smtpAuth = $mailSmtpAppSetting->getSmtpAuth(),
            "smtpSecure" => $smtpSecure = $mailSmtpAppSetting->getSmtpSecure(),
            "timeout" => $timeout = $mailSmtpAppSetting->getTimeout(),
        ]);

        $MailSmtpAppSettingTesting = $this
            ->appSettingService
            ->getValue($appSetting, MailSmtpAppSetting::class)
        ;

        $this->assertEquals($MailSmtpAppSettingTesting->getHost(), $host);
        $this->assertEquals($MailSmtpAppSettingTesting->getPort(), $port);
        $this->assertEquals($MailSmtpAppSettingTesting->getFromAddress(), $fromAddress);
        $this->assertEquals($MailSmtpAppSettingTesting->getUsername(), $username);
        $this->assertEquals($MailSmtpAppSettingTesting->getPassword(), $password);
        $this->assertEquals($MailSmtpAppSettingTesting->getSmtpAuth(), $smtpAuth);
        $this->assertEquals($MailSmtpAppSettingTesting->getSmtpSecure(), $smtpSecure);
        $this->assertEquals($MailSmtpAppSettingTesting->getTimeout(), $timeout);
    }

    #[DataProvider('mailSmtpAppSettingProvider')]
    public function testUpdateValue(MailSmtpAppSetting $mailSmtpAppSetting): void
    {   
        $appSetting = new AppSetting();

        $appSetting->setKey("test.key");
        $appSetting->setValue([
            "host" => $mailSmtpAppSetting->getHost(),
            "port" => $mailSmtpAppSetting->getPort(),
            "fromAddress" => $mailSmtpAppSetting->getFromAddress(),
            "username" => $mailSmtpAppSetting->getUsername(),
            "password" => $mailSmtpAppSetting->getPassword(),
            "smtpAuth" => $mailSmtpAppSetting->getSmtpAuth(),
            "smtpSecure" => $mailSmtpAppSetting->getSmtpSecure(),
            "timeout" => $mailSmtpAppSetting->getTimeout(),
        ]);

        $appSetting = $this
            ->appSettingService
            ->updateValue($appSetting, [
                "host" => "super@updated.host",
                "port" => "776",
                "fromAddress" => "updated_from_address@gmail.com",
            ])
        ;

        $mailSmtpAppSettingTesting = $this
            ->appSettingService
            ->getValue($appSetting, MailSmtpAppSetting::class)
        ;

        $this->assertEquals($mailSmtpAppSettingTesting->getHost(), "super@updated.host");
        $this->assertEquals($mailSmtpAppSettingTesting->getPort(), "776");
        $this->assertEquals($mailSmtpAppSettingTesting->getFromAddress(), "updated_from_address@gmail.com");
        
        $this->assertEquals($mailSmtpAppSettingTesting->getUsername(), "");
        $this->assertEquals($mailSmtpAppSettingTesting->getPassword(), "");
        $this->assertEquals($mailSmtpAppSettingTesting->getSmtpAuth(), false);
        $this->assertEquals($mailSmtpAppSettingTesting->getSmtpSecure(), "");
        $this->assertEquals($mailSmtpAppSettingTesting->getTimeout(), 0);
    }

    public static function mailSmtpAppSettingProvider(): array
    {
        return [
            [new MailSmtpAppSetting(
                host: "test@gmail.com", 
                port: "523", 
                fromAddress: "test@gmail.com"
            )],
            [new MailSmtpAppSetting(
                timeout: 100, 
                smtpAuth: true, 
                password: "test"
            )],
            [new MailSmtpAppSetting(
                host: "super_test@host.com", 
                port: "123", 
                fromAddress: "test@gmail.com", 
                username: "user test", 
                password: "test super password",
                smtpAuth: true,
                smtpSecure: "ssl",
                timeout: 150
            )],
            [new MailSmtpAppSetting(
                port: "411", 
                smtpSecure: "test test secure"
            )],
            [new MailSmtpAppSetting()]
        ];
    }
}
