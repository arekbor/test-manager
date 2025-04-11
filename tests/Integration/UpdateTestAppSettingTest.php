<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Command\UpdateTestAppSetting;
use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestClauseAppSetting;
use App\Application\AppSetting\Model\TestMessageAppSetting;
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

        $testAppSettingAfterUpdate->addTestMessage(
            (new TestMessageAppSetting())
                ->setIntroduction('Test introduction message')
                ->setConclusion('Test conclusion message')
                ->setLanguage('en')
        );

        $testAppSettingAfterUpdate->addTestMessage(
            (new TestMessageAppSetting())
                ->setIntroduction('Testowa wiadomość powitalna')
                ->setConclusion('Testowa wiadomość pożegnalna')
                ->setLanguage('pl')
        );

        $testAppSettingAfterUpdate->addTestClause(
            (new TestClauseAppSetting())
                ->setContent('Test clause message')
                ->setLanguage('en')
        );

        $testAppSettingAfterUpdate->addTestClause(
            (new TestClauseAppSetting())
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

        $this->assertCount(2, $testAppSetting->getTestMessages());

        $this->assertEquals('Test introduction message', $testAppSetting->getTestMessages()[0]->getIntroduction());
        $this->assertEquals('Test conclusion message', $testAppSetting->getTestMessages()[0]->getConclusion());
        $this->assertEquals('en', $testAppSetting->getTestMessages()[0]->getLanguage());

        $this->assertEquals('Testowa wiadomość powitalna', $testAppSetting->getTestMessages()[1]->getIntroduction());
        $this->assertEquals('Testowa wiadomość pożegnalna', $testAppSetting->getTestMessages()[1]->getConclusion());
        $this->assertEquals('pl', $testAppSetting->getTestMessages()[1]->getLanguage());

        $this->assertCount(2, $testAppSetting->getTestClauses());

        $this->assertEquals('Test clause message', $testAppSetting->getTestClauses()[0]->getContent());
        $this->assertEquals('en', $testAppSetting->getTestClauses()[0]->getLanguage());

        $this->assertEquals('Testowa klauzula', $testAppSetting->getTestClauses()[1]->getContent());
        $this->assertEquals('pl', $testAppSetting->getTestClauses()[1]->getLanguage());
    }
}