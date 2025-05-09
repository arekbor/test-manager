<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Shared\QueryBusInterface;
use App\Application\Module\Model\ModuleModel;
use App\Application\Module\Query\GetModuleModel;
use App\Domain\Entity\Module;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Uuid;

final class GetModuleModelTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly QueryBusInterface $queryBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryBus = self::getContainer()->get(QueryBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetModuleModelQueryReturnsModuleSuccessfully(): void
    {
        //Arrange
        $module = new Module();
        $module->setName('Test to find by query');
        $module->setLanguage('en');
        $module->setCategory('introduction');

        $this->entityManager->persist($module);
        $this->entityManager->flush();

        $query = new GetModuleModel($module->getId());

        //Act

        /**
         * @var ModuleModel $moduleModel
         */
        $moduleModel = $this->queryBus->query($query);

        //Assert
        $this->assertInstanceOf(ModuleModel::class, $moduleModel);
        $this->assertEquals('Test to find by query', $moduleModel->getName());
        $this->assertEquals('en', $moduleModel->getLanguage());
        $this->assertEquals('introduction', $moduleModel->getCategory());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetModuleModelQueryThrowsExceptionWhenModuleNotFound(): void
    {
        $notExistingModuleId = Uuid::v4();

        $query = new GetModuleModel($notExistingModuleId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Module {"id":"%s"}', $notExistingModuleId->toString()));

        $this->queryBus->query($query);
    }
}