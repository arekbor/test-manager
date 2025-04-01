<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Module\Command\CreateModule;
use App\Application\Module\Model\ModuleModel;
use App\Domain\Entity\Module;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateModuleTest extends DatabaseTestCase
{
    private readonly MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('command.bus');
    }

    #[Test]
    #[Group("Integration")]
    public function testCreateModuleCommandSuccessfullyPersistsModule(): void
    {
        //Arrange
        $moduleModel = new ModuleModel();
        $moduleModel->setName('Create test module name');
        $moduleModel->setLanguage('en');
        $moduleModel->setCategory('introduction');

        $command = new CreateModule($moduleModel);

        //Act
        $this->commandBus->dispatch($command);

        $repo = $this->entityManager->getRepository(Module::class);

        /**
         * @var Module $module
         */
        $module = $repo->findOneBy(['name' => 'Create test module name']);

        //Assert
        $this->assertNotEmpty($module->getId());
        $this->assertEquals('Create test module name', $module->getName());
        $this->assertEquals('en', $module->getLanguage());
        $this->assertEquals('introduction', $module->getCategory());
        $this->assertEquals(0, $module->getQuestions()->count());
        $this->assertEquals(0, $module->getVideos()->count());
    }
}