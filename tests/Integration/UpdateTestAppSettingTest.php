<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Command\UpdateTestAppSetting;
use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestMessageAppSetting;
use App\Application\AppSetting\Model\TestPrivacyPolicyAppSetting;
use App\Domain\Entity\AppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdateTestAppSettingTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly MessageBusInterface $commandBus;
    private readonly AppSettingDecoder $appSettingDecoder;
    private readonly AppSettingManager $appSettingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->appSettingDecoder = $container->get(AppSettingDecoder::class);

        $this->appSettingManager = $container->get(AppSettingManager::class);

        $this->commandBus = $container->get('command.bus');
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateTestAppSettingSuccessfullyPersistsAppSetting(): void
    {
        //Arrange
        $testAppSettingBeforeUpdate = new TestAppSetting();

        $testAppSetting = new AppSetting();
        $testAppSetting->setKey(TestAppSetting::APP_SETTING_KEY);
        
        $encodedValue = $this->appSettingDecoder->decode($testAppSettingBeforeUpdate);
        $testAppSetting->setValue($encodedValue);

        $this->entityManager->persist($testAppSetting);
        $this->entityManager->flush();

        $testAppSettingAfterUpdate = new TestAppSetting();
        $testAppSettingAfterUpdate->setExpirationDaysOffset(32);
        $testAppSettingAfterUpdate->setNotificationsEnabled(true);

        $testAppSettingAfterUpdate->addTestMessageAppSetting(
            (new TestMessageAppSetting())
                ->setIntroduction('Test introduction message')
                ->setConclusion('Test conclusion message')
                ->setLanguage('en')
        );

        $testAppSettingAfterUpdate->addTestMessageAppSetting(
            (new TestMessageAppSetting())
                ->setIntroduction('Testowa wiadomość powitalna')
                ->setConclusion('Testowa wiadomość pożegnalna')
                ->setLanguage('pl')
        );

        $testAppSettingAfterUpdate->addTestPrivacyPolicyAppSetting(
            (new TestPrivacyPolicyAppSetting())
                ->setContent('Test privacy policy message')
                ->setLanguage('en')
        );

        $testAppSettingAfterUpdate->addTestPrivacyPolicyAppSetting(
            (new TestPrivacyPolicyAppSetting())
                ->setContent('Testowa klauzula')
                ->setLanguage('pl')
        );

        $command = new UpdateTestAppSetting($testAppSettingAfterUpdate);

        //Act
        $this->commandBus->dispatch($command);

        /**
         * @var AppSetting $appSetting
         */
        $appSetting = $this->entityManager->getRepository(AppSetting::class)
            ->findOneBy(['key' => TestAppSetting::APP_SETTING_KEY])
        ;

        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingManager->get($appSetting, TestAppSetting::class);

        //Assert
        $this->assertInstanceOf(AppSetting::class, $appSetting);
        $this->assertNotNull($appSetting->getId());

        $this->assertInstanceOf(TestAppSetting::class, $testAppSetting);

        $this->assertEquals(32, $testAppSetting->getExpirationDaysOffset());
        $this->assertTrue($testAppSetting->getNotificationsEnabled());

        $testMessageAppSettings = $testAppSetting->getTestMessageAppSettings();

        $this->assertCount(2, $testMessageAppSettings);

        $this->assertEquals('Test introduction message', $testMessageAppSettings[0]->getIntroduction());
        $this->assertEquals('Test conclusion message', $testMessageAppSettings[0]->getConclusion());
        $this->assertEquals('en', $testMessageAppSettings[0]->getLanguage());

        $this->assertEquals('Testowa wiadomość powitalna', $testMessageAppSettings[1]->getIntroduction());
        $this->assertEquals('Testowa wiadomość pożegnalna', $testMessageAppSettings[1]->getConclusion());
        $this->assertEquals('pl', $testMessageAppSettings[1]->getLanguage());

        $testPrivacyPolicyAppSettings = $testAppSetting->getTestPrivacyPolicyAppSettings();

        $this->assertCount(2, $testPrivacyPolicyAppSettings);

        $this->assertEquals('Test privacy policy message', $testPrivacyPolicyAppSettings[0]->getContent());
        $this->assertEquals('en', $testPrivacyPolicyAppSettings[0]->getLanguage());

        $this->assertEquals('Testowa klauzula', $testPrivacyPolicyAppSettings[1]->getContent());
        $this->assertEquals('pl', $testPrivacyPolicyAppSettings[1]->getLanguage());
    }
}