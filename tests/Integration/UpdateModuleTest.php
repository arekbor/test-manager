<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Module\Command\UpdateModule;
use App\Application\Module\Model\UpdateModuleModel;
use App\Domain\Entity\Module;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateModuleTest extends DatabaseTestCase
{
    private readonly MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('command.bus');
    }

    #[Test]
    #[Group("Integration")]
    public function testUpdateModuleUpdatesCorreclty(): void
    {
        //Arrange
        $module = new Module();
        $module->setName('Module before update');
        $module->setLanguage('pl');
        $module->setCategory('introduction');

        $this->entityManager->persist($module);
        $this->entityManager->flush();

        $updateModuleModel = new UpdateModuleModel();
        $updateModuleModel->setName('Updated Test Module');
        $updateModuleModel->setLanguage('en');
        $updateModuleModel->setCategory('periodic');

        $command = new UpdateModule($module->getId(), $updateModuleModel);

        //Act
        $this->commandBus->dispatch($command);

        $repo = $this->entityManager->getRepository(Module::class);

        /**
         * @var Module $updatedModule
         */
        $updatedModule = $repo->find($module->getId());

        //Assert
        $this->assertEquals($module->getId(), $updatedModule->getId());
        $this->assertEquals('Updated Test Module', $updatedModule->getName());
        $this->assertEquals('en', $updatedModule->getLanguage());
        $this->assertEquals('periodic', $updatedModule->getCategory());
    }

    #[Test]
    #[Group("Integration")]
    public function testUpdatThrowsExceptionWhenModuleNotFound(): void
    {
        $updateModuleModel = new UpdateModuleModel();
        $updateModuleModel->setName('Updated Test Module');
        $updateModuleModel->setLanguage('en');
        $updateModuleModel->setCategory('periodic');

        $notExistingModuleId = Uuid::v4();

        $command = new UpdateModule($notExistingModuleId, $updateModuleModel);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Module {"id":"%s"}', $notExistingModuleId->toString()));

        $this->commandBus->dispatch($command);
    }
}