<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Command\UpdateMailSmtpAppSetting\UpdateMailSmtpAppSetting;
use App\Domain\Entity\AppSetting;
use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

final class UpdateMailSmtpAppSettingTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly CommandBusInterface $commandBus;
    private readonly AppSettingDecoder $appSettingDecoder;
    private readonly AppSettingManager $appSettingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->appSettingDecoder = $container->get(AppSettingDecoder::class);
        $this->appSettingManager = $container->get(AppSettingManager::class);
        $this->commandBus = $container->get(CommandBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateMailSmtpAppSettingSuccessfullyPersistsAppSetting(): void
    {
        //Arrange
        $mailSmtpAppSettingBeforeUpdate = new MailSmtpAppSetting();

        $testAppSetting = new AppSetting();
        $testAppSetting->setKey(MailSmtpAppSetting::APP_SETTING_KEY);

        $encodedValue = $this->appSettingDecoder->decode($mailSmtpAppSettingBeforeUpdate);
        $testAppSetting->setValue($encodedValue);

        $this->entityManager->persist($testAppSetting);
        $this->entityManager->flush();

        $mailSmtpAppSettingAfterUpdate = new MailSmtpAppSetting(
            host: 'test.host.com',
            port: '546',
            fromAddress: 'test@gmail.com',
            username: 'test@gmail.com',
            password: 'secret',
            smtpAuth: true,
            smtpSecure: 'ssl',
            timeout: 10
        );

        $command = new UpdateMailSmtpAppSetting($mailSmtpAppSettingAfterUpdate);

        //Act
        $this->commandBus->handle($command);

        /**
         * @var AppSetting $appSetting
         */
        $appSetting = $this->entityManager->getRepository(AppSetting::class)
            ->findOneBy(['key' => MailSmtpAppSetting::APP_SETTING_KEY]);

        /**
         * @var MailSmtpAppSetting $mailSmtpAppSetting
         */
        $mailSmtpAppSetting = $this->appSettingManager->get($appSetting, MailSmtpAppSetting::class);

        //Assert
        $this->assertInstanceOf(AppSetting::class, $appSetting);
        $this->assertNotNull($appSetting->getId());

        $this->assertInstanceOf(MailSmtpAppSetting::class, $mailSmtpAppSetting);

        $this->assertEquals('test.host.com', $mailSmtpAppSetting->getHost());
        $this->assertEquals('546', $mailSmtpAppSetting->getPort());
        $this->assertEquals('test@gmail.com', $mailSmtpAppSetting->getFromAddress());
        $this->assertEquals('test@gmail.com', $mailSmtpAppSetting->getUsername());
        $this->assertTrue($mailSmtpAppSetting->getSmtpAuth());
        $this->assertEquals('ssl', $mailSmtpAppSetting->getSmtpSecure());
        $this->assertEquals(10, $mailSmtpAppSetting->getTimeout());

        $this->assertNotEmpty($mailSmtpAppSetting->getPassword());
        $this->assertNotEquals('secret', $mailSmtpAppSetting->getPassword());
    }
}
