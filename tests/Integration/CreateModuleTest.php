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
    use IntegrationTestTrait;

    private readonly MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('command.bus');
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
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

        /**
         * @var Module $module
         */
        $module = $this->entityManager->getRepository(Module::class)->findOneBy(['name' => 'Create test module name']);

        //Assert
        $this->assertInstanceOf(Module::class, $module);
        $this->assertNotNull($module->getId());
        $this->assertEquals('Create test module name', $module->getName());
        $this->assertEquals('en', $module->getLanguage());
        $this->assertEquals('introduction', $module->getCategory());

        $this->assertCount(0, $module->getQuestions());
        $this->assertCount(0, $module->getVideos());
    }
}