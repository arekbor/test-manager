<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Module\Command\UpdateModule;
use App\Application\Module\Model\ModuleModel;
use App\Domain\Entity\Module;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateModuleTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('command.bus');
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateModuleCommandSuccessfullyUpdatesModule(): void
    {
        //Arrange
        $module = new Module();
        $module->setName('Module before update');
        $module->setLanguage('pl');
        $module->setCategory('introduction');

        $this->entityManager->persist($module);
        $this->entityManager->flush();

        $moduleModel = new ModuleModel();
        $moduleModel->setName('Updated Test Module');
        $moduleModel->setLanguage('en');
        $moduleModel->setCategory('periodic');

        $command = new UpdateModule($module->getId(), $moduleModel);

        //Act
        $this->commandBus->dispatch($command);

        /**
         * @var Module $updatedModule
         */
        $updatedModule = $this->entityManager->getRepository(Module::class)->find($module->getId());

        //Assert
        $this->assertInstanceOf(Module::class, $updatedModule);
        $this->assertEquals($module->getId(), $updatedModule->getId());
        $this->assertEquals('Updated Test Module', $updatedModule->getName());
        $this->assertEquals('en', $updatedModule->getLanguage());
        $this->assertEquals('periodic', $updatedModule->getCategory());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateModuleCommandThrowsExceptionWhenModuleNotFound(): void
    {
        $notExistingModuleId = Uuid::v4();

        $command = new UpdateModule($notExistingModuleId, new ModuleModel());

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Module {"id":"%s"}', $notExistingModuleId->toString()));

        $this->commandBus->dispatch($command);
    }
}