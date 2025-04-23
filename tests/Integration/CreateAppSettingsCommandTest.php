<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Application\AppSetting\Model\TestAppSetting;
use App\Domain\Entity\AppSetting;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateAppSettingsCommandTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testCreateAppSettingsCommandPersistsAppSettingsCorrectly(): void
    {
        //Arrange
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-app-settings');

        $commandTester = new CommandTester($command);

        //Act
        $commandTester->execute([]);

        $repo = $this->entityManager->getRepository(AppSetting::class);

        /**
         * @var AppSetting $mailSmtpAppSetting
         */
        $mailSmtpAppSetting = $repo->findOneBy(['key' => MailSmtpAppSetting::APP_SETTING_KEY]);

        /**
         * @var AppSetting $testAppSetting
         */
        $testAppSetting = $repo->findOneBy(['key' => TestAppSetting::APP_SETTING_KEY]);

        //Assert
        $this->assertInstanceOf(AppSetting::class, $mailSmtpAppSetting);
        $this->assertNotNull($mailSmtpAppSetting->getId());
        $this->assertEquals('mail.smtp', $mailSmtpAppSetting->getKey());
        $this->assertIsArray($mailSmtpAppSetting->getValue());
        $this->assertEquals([
            'host' => '',
            'port' => '',
            'fromAddress' => '',
            'username' => '',
            'password' => '',
            'smtpAuth' => false,
            'smtpSecure' => '',
            'timeout' => 0
        ], $mailSmtpAppSetting->getValue());

        $this->assertInstanceOf(AppSetting::class, $testAppSetting);
        $this->assertNotNull($testAppSetting->getId());
        $this->assertEquals('test', $testAppSetting->getKey());
        $this->assertIsArray($testAppSetting->getValue());
        $this->assertEquals([
            'expirationDaysOffset' => 7,
            'testMessageAppSettings' => [],
            'notificationsEnabled' => true,
            'testPrivacyPolicyAppSettings' => []
        ], $testAppSetting->getValue());
    }
}