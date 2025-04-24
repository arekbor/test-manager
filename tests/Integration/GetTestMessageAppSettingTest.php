<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestMessageAppSetting;
use App\Application\Shared\QueryBusInterface;
use App\Application\Test\Query\GetTestMessageAppSetting;
use App\Domain\Entity\AppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

final class GetTestMessageAppSettingTest extends DatabaseTestCase
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
    public function testGetTestMessageAppSettingReturnsTestMessageAppSettingByProvidedLanguageCorrectly(): void
    {
        //Arrange
        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);

        $testTestAppSetting = new TestAppSetting();
        $testTestAppSetting->addTestMessageAppSetting(
            (new TestMessageAppSetting())
                ->setIntroduction('Introduction message')
                ->setConclusion('Conclusion message')
                ->setLanguage('en')
        );

        $encodedValue = $this->appSettingDecoder->decode($testTestAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetTestMessageAppSetting('en');

        //Act
        /**
         * @var TestMessageAppSetting $testMessageAppSetting
         */
        $testMessageAppSetting = $this->queryBus->query($query);

        //Assert
        $this->assertInstanceOf(TestMessageAppSetting::class, $testMessageAppSetting);
        $this->assertEquals('Introduction message', $testMessageAppSetting->getIntroduction());
        $this->assertEquals('Conclusion message', $testMessageAppSetting->getConclusion());
        $this->assertEquals('en', $testMessageAppSetting->getLanguage());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetTestMessageAppSettingReturnsNullWhenTestMessageAppSettingByProvidedLanguageNotFound(): void
    {
        //Arrange
        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);

        $testTestAppSetting = new TestAppSetting();
        $testTestAppSetting->addTestMessageAppSetting(
            (new TestMessageAppSetting())
                ->setIntroduction('Introduction message')
                ->setConclusion('Conclusion message')
                ->setLanguage('fr')
        );

        $encodedValue = $this->appSettingDecoder->decode($testTestAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetTestMessageAppSetting('en');
        
        //Act
        /**
         * @var TestMessageAppSetting|null $testMessageAppSetting
         */
        $testMessageAppSetting = $this->queryBus->query($query);

        //Assert
        $this->assertNull($testMessageAppSetting);
    }
}