<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestMessageAppSetting;
use App\Application\AppSetting\Model\TestPrivacyPolicyAppSetting;
use App\Application\AppSetting\Query\GetTestAppSetting\GetTestAppSetting;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Domain\Entity\AppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

final class GetTestAppSettingTest extends DatabaseTestCase
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
    public function testGetMailSmtpAppSettingQueryReturnsTestAppSettingCorrectly(): void
    {
        //Arrange
        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);

        $testTestAppSetting = new TestAppSetting();
        $testTestAppSetting->setExpirationDaysOffset(15);
        $testTestAppSetting->setNotificationsEnabled(true);
        $testTestAppSetting->addTestMessageAppSetting(
            (new TestMessageAppSetting())
                ->setIntroduction('Introduction message')
                ->setConclusion('Conclusion message')
                ->setLanguage('en')
        );

        $testTestAppSetting->addTestMessageAppSetting(
            (new TestMessageAppSetting())
                ->setIntroduction('Powitalna wiadomość')
                ->setConclusion('Pożegnalna wiadomość')
                ->setLanguage('pl')
        );

        $testTestAppSetting->addTestPrivacyPolicyAppSetting(
            (new TestPrivacyPolicyAppSetting())
                ->setContent('Privacy policy message')
                ->setLanguage('en')
        );

        $testTestAppSetting->addTestPrivacyPolicyAppSetting(
            (new TestPrivacyPolicyAppSetting())
                ->setContent('Klauzula rodo')
                ->setLanguage('pl')
        );

        $encodedValue = $this->appSettingDecoder->decode($testTestAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetTestAppSetting();

        //Act
        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->queryBus->ask($query);

        //Assert
        $this->assertInstanceOf(TestAppSetting::class, $testAppSetting);
        $this->assertEquals(15, $testAppSetting->getExpirationDaysOffset());
        $this->assertTrue($testAppSetting->getNotificationsEnabled());

        $testMessageAppSettings = $testAppSetting->getTestMessageAppSettings();

        $this->assertCount(2, $testMessageAppSettings);

        $this->assertEquals('Introduction message', $testMessageAppSettings[0]->getIntroduction());
        $this->assertEquals('Conclusion message', $testMessageAppSettings[0]->getConclusion());
        $this->assertEquals('en', $testMessageAppSettings[0]->getLanguage());

        $this->assertEquals('Powitalna wiadomość', $testMessageAppSettings[1]->getIntroduction());
        $this->assertEquals('Pożegnalna wiadomość', $testMessageAppSettings[1]->getConclusion());
        $this->assertEquals('pl', $testMessageAppSettings[1]->getLanguage());

        $testPrivacyPolicyAppSettings = $testAppSetting->getTestPrivacyPolicyAppSettings();

        $this->assertCount(2, $testPrivacyPolicyAppSettings);

        $this->assertEquals('Privacy policy message', $testPrivacyPolicyAppSettings[0]->getContent());
        $this->assertEquals('en', $testPrivacyPolicyAppSettings[0]->getLanguage());

        $this->assertEquals('Klauzula rodo', $testPrivacyPolicyAppSettings[1]->getContent());
        $this->assertEquals('pl', $testPrivacyPolicyAppSettings[1]->getLanguage());
    }
}
