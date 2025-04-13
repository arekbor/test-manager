<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Application\AppSetting\Query\GetMailSmtpAppSetting;
use App\Application\Shared\QueryBusInterface;
use App\Domain\Entity\AppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

final class GetMailSmtpAppSettingTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly QueryBusInterface $queryBus;
    private readonly AppSettingDecoder $appSettingDecoder;
    private readonly AppSettingManager $appSettingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->queryBus = $container->get(QueryBusInterface::class);

        $this->appSettingDecoder = $container->get(AppSettingDecoder::class);

        $this->appSettingManager = $container->get(AppSettingManager::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetMailSmtpAppSettingQueryReturnsMailSmtpAppSettingCorrectly(): void
    {
        //Arrange
        $appSetting = new AppSetting();
        $appSetting->setKey(MailSmtpAppSetting::APP_SETTING_KEY);

        $testMailSmtpAppSetting = new MailSmtpAppSetting(
            host: 'test.host.com',
            port: '256',
            fromAddress: 'user@gmail.com',
            username: 'test_user@gmail.com',
            password: 'secret',
            smtpAuth: false,
            smtpSecure: 'ssl',
            timeout: 15
        );

        $encodedValue = $this->appSettingDecoder->decode($testMailSmtpAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetMailSmtpAppSetting();

        //Act
        /**
         * @var MailSmtpAppSetting $mailSmtpAppSetting
         */
        $mailSmtpAppSetting = $this->queryBus->query($query);

        //Assert
        $this->assertInstanceOf(MailSmtpAppSetting::class, $mailSmtpAppSetting);
        $this->assertEquals('test.host.com', $mailSmtpAppSetting->getHost());
        $this->assertEquals('256', $mailSmtpAppSetting->getPort());
        $this->assertEquals('user@gmail.com', $mailSmtpAppSetting->getFromAddress());
        $this->assertEquals('test_user@gmail.com', $mailSmtpAppSetting->getUsername());
        $this->assertEquals('secret', $mailSmtpAppSetting->getPassword());
        $this->assertFalse($mailSmtpAppSetting->getSmtpAuth());
        $this->assertEquals('ssl', $mailSmtpAppSetting->getSmtpSecure());
        $this->assertEquals(15, $mailSmtpAppSetting->getTimeout());
    }
}