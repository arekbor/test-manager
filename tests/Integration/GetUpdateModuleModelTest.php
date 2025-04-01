<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Module\Query\GetUpdateModuleModel;
use App\Application\Shared\QueryBusInterface;
use App\Application\Module\Model\UpdateModuleModel;
use App\Domain\Entity\Module;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Uuid;

final class GetUpdateModuleModelTest extends DatabaseTestCase
{
    private readonly QueryBusInterface $queryBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryBus = self::getContainer()->get(QueryBusInterface::class);
    }

    #[Test]
    #[Group("Integration")]
    public function testQueryFindsGetUpdateModuleModelCorreclty(): void
    {
        //Arrange
        $module = new Module();
        $module->setName('Test to find by query');
        $module->setLanguage('en');
        $module->setCategory('introduction');

        $this->entityManager->persist($module);
        $this->entityManager->flush();

        $query = new GetUpdateModuleModel($module->getId());

        //Act

        /**
         * @var UpdateModuleModel $updateModuleModel
         */
        $updateModuleModel = $this->queryBus->query($query);

        //Assert
        $this->assertEquals('Test to find by query', $updateModuleModel->getName());
        $this->assertEquals('en', $updateModuleModel->getLanguage());
        $this->assertEquals('introduction', $updateModuleModel->getCategory());
    }

    #[Test]
    #[Group("Integration")]
    public function testQueryThrowsExceptionWhenModuleNotFound(): void
    {
        $notExistingModuleId = Uuid::v4();

        $query = new GetUpdateModuleModel($notExistingModuleId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Module {"id":"%s"}', $notExistingModuleId->toString()));

        $this->queryBus->query($query);
    }
}