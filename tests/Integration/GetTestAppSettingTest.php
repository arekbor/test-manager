<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestClauseAppSetting;
use App\Application\AppSetting\Model\TestMessageAppSetting;
use App\Application\AppSetting\Query\GetTestAppSetting;
use App\Application\Shared\QueryBusInterface;
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
        $testTestAppSetting->addTestMessage(
            (new TestMessageAppSetting())
                ->setIntroduction('Introduction message')
                ->setConclusion('Conclusion message')
                ->setLanguage('en')
        );

        $testTestAppSetting->addTestMessage(
            (new TestMessageAppSetting())
                ->setIntroduction('Powitalna wiadomość')
                ->setConclusion('Pożegnalna wiadomość')
                ->setLanguage('pl')
        );

        $testTestAppSetting->addTestClause(
            (new TestClauseAppSetting())
                ->setContent('Clause message')
                ->setLanguage('en')
        );

        $testTestAppSetting->addTestClause(
            (new TestClauseAppSetting())
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
        $testAppSetting = $this->queryBus->query($query);

        //Assert
        $this->assertInstanceOf(TestAppSetting::class, $testAppSetting);
        $this->assertEquals(15, $testAppSetting->getExpirationDaysOffset());
        $this->assertTrue($testAppSetting->getNotificationsEnabled());

        /**
         * @var TestMessageAppSetting[] $testMessageAppSettings
         */
        $testMessageAppSettings = $testAppSetting->getTestMessages();

        $this->assertCount(2, $testMessageAppSettings);

        $this->assertEquals('Introduction message', $testMessageAppSettings[0]->getIntroduction());
        $this->assertEquals('Conclusion message', $testMessageAppSettings[0]->getConclusion());
        $this->assertEquals('en', $testMessageAppSettings[0]->getLanguage());

        $this->assertEquals('Powitalna wiadomość', $testMessageAppSettings[1]->getIntroduction());
        $this->assertEquals('Pożegnalna wiadomość', $testMessageAppSettings[1]->getConclusion());
        $this->assertEquals('pl', $testMessageAppSettings[1]->getLanguage());

        /**
         * @var TestClauseAppSetting[] $testClauseAppSettings
         */
        $testClauseAppSettings = $testAppSetting->getTestClauses();

        $this->assertCount(2, $testClauseAppSettings);

        $this->assertEquals('Clause message', $testClauseAppSettings[0]->getContent());
        $this->assertEquals('en', $testClauseAppSettings[0]->getLanguage());

        $this->assertEquals('Klauzula rodo', $testClauseAppSettings[1]->getContent());
        $this->assertEquals('pl', $testClauseAppSettings[1]->getLanguage());
    }
}