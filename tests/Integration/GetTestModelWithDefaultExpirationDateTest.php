<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\DatabaseTestCase;
use App\Domain\Entity\AppSetting;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Test\Model\TestModel;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\AppSetting\Model\TestAppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Application\Test\Query\GetTestModelWithDefaultExpirationDate\GetTestModelWithDefaultExpirationDate;

final class GetTestModelWithDefaultExpirationDateTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly QueryBusInterface $queryBus;
    private readonly AppSettingDecoder $appSettingDecoder;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->queryBus = $container->get(QueryBusInterface::class);
        $this->appSettingDecoder = $container->get(AppSettingDecoder::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetTestModelWithDefaultExpirationDateReturnsTestModelWithCorrectExpirationDate(): void
    {
        //Arrange
        $testAppSetting = new TestAppSetting();
        $testAppSetting->setExpirationDaysOffset(12);

        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);

        $encodedValue = $this->appSettingDecoder->decode($testAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetTestModelWithDefaultExpirationDate();

        //Act
        /**
         * @var TestModel $testModel
         */
        $testModel = $this->queryBus->ask($query);

        //Assert
        $this->assertInstanceOf(TestModel::class, $testModel);

        $this->assertEqualsWithDelta(
            (new \DateTime())->modify('+12 days')->getTimestamp(),
            $testModel->getExpiration()->getTimestamp(),
            1
        );
    }
}
