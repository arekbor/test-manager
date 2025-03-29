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
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    #[Group("Integration")]
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
         * @var AppSetting $mailSmtpAppSettingRaw
         */
        $mailSmtpAppSettingRaw = $repo->findOneBy(['key' => MailSmtpAppSetting::APP_SETTING_KEY]);

        /**
         * @var AppSetting $testAppSettingRaw
         */
        $testAppSettingRaw = $repo->findOneBy(['key' => TestAppSetting::APP_SETTING_KEY]);

        //Assert
        $this->assertEquals('mail.smtp', $mailSmtpAppSettingRaw->getKey());
        $this->assertIsArray($mailSmtpAppSettingRaw->getValue());
        $this->assertEquals([
            'host' => '',
            'port' => '',
            'fromAddress' => '',
            'username' => '',
            'password' => '',
            'smtpAuth' => false,
            'smtpSecure' => '',
            'timeout' => 0
        ], $mailSmtpAppSettingRaw->getValue());

        $this->assertEquals('test', $testAppSettingRaw->getKey());
        $this->assertIsArray($testAppSettingRaw->getValue());
        $this->assertEquals([
            'expirationDaysOffset' => 7,
            'testMessages' => [],
            'notificationsEnabled' => true,
            'testClauses' => []
        ], $testAppSettingRaw->getValue());
    }
}