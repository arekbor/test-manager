<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestClauseAppSetting;
use App\Application\Shared\QueryBusInterface;
use App\Application\Test\Query\GetTestClauseAppSetting;
use App\Domain\Entity\AppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

final class GetTestClauseAppSettingTest extends DatabaseTestCase
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
    public function testGetTestClauseAppSettingReturnsTestClauseAppSettingByProvidedLanguageCorrectly(): void
    {
        //Arrange
        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);

        $testTestAppSetting = new TestAppSetting();
        $testTestAppSetting->addTestClause(
            (new TestClauseAppSetting())
                ->setContent('Clause message')
                ->setLanguage('pl')
        );

        $encodedValue = $this->appSettingDecoder->decode($testTestAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetTestClauseAppSetting('pl');

        //Act
        /**
         * @var TestClauseAppSetting $testClauseAppSetting
         */
        $testClauseAppSetting = $this->queryBus->query($query);

        //Assert
        $this->assertInstanceOf(TestClauseAppSetting::class, $testClauseAppSetting);
        $this->assertEquals('Clause message', $testClauseAppSetting->getContent());
        $this->assertEquals('pl', $testClauseAppSetting->getLanguage());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetTestClauseAppSettingReturnsNullWhenTestClauseAppSettingByProvidedLanguageNotFound(): void
    {
        //Arrange
        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);

        $testTestAppSetting = new TestAppSetting();
        $testTestAppSetting->addTestClause(
            (new TestClauseAppSetting())
                ->setContent('Clause message')
                ->setLanguage('fr')
        );

        $encodedValue = $this->appSettingDecoder->decode($testTestAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetTestClauseAppSetting('pl');

        //Act
        /**
         * @var TestClauseAppSetting|null $testClauseAppSetting
         */
        $testClauseAppSetting = $this->queryBus->query($query);

        //Assert
        $this->assertNull($testClauseAppSetting);
    }
}