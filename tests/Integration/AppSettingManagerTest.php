<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\DTO\AppSettingToCreate;
use App\Domain\Entity\AppSetting;
use App\Domain\Model\MailSmtpAppSetting;
use App\Domain\Model\TestAppSetting;
use App\Infrastructure\AppSetting\Repository\AppSettingRepository;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Infrastructure\Shared\UnitOfWork;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class AppSettingManagerTest extends DatabaseTestCase
{
    private readonly AppSettingManager $appSettingManager;
    private readonly AppSettingDecoder $appSettingDecoder;

    protected function setUp(): void
    {
        parent::setUp();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $this->appSettingDecoder = new AppSettingDecoder($serializer);

        $appSettingRepository = new AppSettingRepository($this->entityManager);
        $unitOfWork = new UnitOfWork($this->entityManager);

        $this->appSettingManager = new AppSettingManager($this->appSettingDecoder, $appSettingRepository, $unitOfWork);
    }

    #[Test]
    #[Group("Integration")]
    public function testCreateManyPersistsAppSettingsCorrectly(): void
    {
        //arrange
        $appSettings = [
            new AppSettingToCreate(MailSmtpAppSetting::APP_SETTING_KEY, new MailSmtpAppSetting()),
            new AppSettingToCreate(TestAppSetting::APP_SETTING_KEY, new TestAppSetting())
        ];

        //act
        $this->appSettingManager->createMany(...$appSettings);

        $repo = $this->entityManager->getRepository(AppSetting::class);

        $mailSmtpAppSetting = $repo->findOneBy(['key' => MailSmtpAppSetting::APP_SETTING_KEY]);
        $testAppSetting = $repo->findOneBy(['key' => TestAppSetting::APP_SETTING_KEY]);

        //assert
        $this->assertNotNull($mailSmtpAppSetting);
        $this->assertNotNull($testAppSetting);
    }

    #[Test]
    #[Group("Integration")]
    public function testGetReturnsCorrectAppSetting(): void
    {
        //Arrange
        $testAppSettingToPersist = new TestAppSetting();
        $testAppSettingToPersist->setExpirationDaysOffset(12);
        $testAppSettingToPersist->setNotificationsEnabled(true);

        $decodedValue = $this->appSettingDecoder->decode($testAppSettingToPersist);

        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);
        $appSetting->setValue($decodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        //Act
        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingManager->get(TestAppSetting::APP_SETTING_KEY, TestAppSetting::class);

        //Assert
        $this->assertEquals(12, $testAppSetting->getExpirationDaysOffset());
        $this->assertEquals(true, $testAppSetting->getNotificationsEnabled());
    }

    #[Test]
    #[Group("Integration")]
    public function testGetThrowsNotFoundExceptionWhenAppSettingNotFound(): void
    {
        $this->expectException(\App\Domain\Exception\NotFoundException::class);
        $this->expectExceptionMessage('App\Domain\Entity\AppSetting {"key":"mail.smtp"}');

        $this->appSettingManager->get(MailSmtpAppSetting::APP_SETTING_KEY, MailSmtpAppSetting::class);
    }

    #[Test]
    #[Group("Integration")]
    public function testUpdateAppSettingPersistsNewValuesCorrectly(): void
    {
        //Arrange
        $mailSmtpAppSettingBeforeUpdate = new MailSmtpAppSetting(
            host: 'gmail.host.com',
            port: '546',
            fromAddress: 'test@gmail.com',
            username: 'username@gmail.com',
            password: 'secret',
            smtpAuth: true,
            smtpSecure: 'ssl',
            timeout: 20
        );

        $decodedValue = $this->appSettingDecoder->decode($mailSmtpAppSettingBeforeUpdate);

        $appSetting = new AppSetting();
        $appSetting->setKey(MailSmtpAppSetting::APP_SETTING_KEY);
        $appSetting->setValue($decodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $mailSmtpAppSettingAfterUpdate = new MailSmtpAppSetting(
            host: 'test.host.com',
            port: '223',
            fromAddress: 'test_user@gmal.com',
            username: 'test123@gmail.com',
            password: 'test secret',
            smtpAuth: false,
            smtpSecure: 'tls',
            timeout: 5
        );

        //Act
        $this->appSettingManager->update(MailSmtpAppSetting::APP_SETTING_KEY, $mailSmtpAppSettingAfterUpdate);

        $repo = $this->entityManager->getRepository(AppSetting::class);

        /**
         * @var AppSetting $appSetting
         */
        $appSetting = $repo->findOneBy(['key' => MailSmtpAppSetting::APP_SETTING_KEY]);

        /**
         * @var MailSmtpAppSetting $mailSmtpAppSetting
         */
        $mailSmtpAppSetting = $this->appSettingDecoder->encode($appSetting->getValue(), MailSmtpAppSetting::class);

        //Assert
        $this->assertEquals('test.host.com', $mailSmtpAppSetting->getHost());
        $this->assertEquals('223', $mailSmtpAppSetting->getPort());
        $this->assertEquals('test_user@gmal.com', $mailSmtpAppSetting->getFromAddress());
        $this->assertEquals('test123@gmail.com', $mailSmtpAppSetting->getUsername());
        $this->assertEquals('test secret', $mailSmtpAppSetting->getPassword());
        $this->assertEquals(false, $mailSmtpAppSetting->getSmtpAuth());
        $this->assertEquals('tls', $mailSmtpAppSetting->getSmtpSecure());
        $this->assertEquals(5, $mailSmtpAppSetting->getTimeout());
    }
}