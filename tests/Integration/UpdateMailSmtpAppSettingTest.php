<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Command\UpdateMailSmtpAppSetting;
use App\Domain\Entity\AppSetting;
use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class UpdateMailSmtpAppSettingTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly MessageBusInterface $commandBus;
    private readonly AppSettingDecoder $appSettingDecoder;
    private readonly AppSettingManager $appSettingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $this->appSettingDecoder = new AppSettingDecoder($serializer);

        $this->appSettingManager = new AppSettingManager($this->appSettingDecoder);

        $this->commandBus = self::getContainer()->get('command.bus');
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateMailSmtpAppSettingSuccessfullyPersistsChanges(): void
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

        $this->commandBus->dispatch($command);

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