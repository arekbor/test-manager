<?php

declare(strict_types = 1);

namespace App\Application\Module\CommandHandler;

use App\Application\Module\Command\CreateModule;
use App\Domain\Entity\Module;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateModuleHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(CreateModule $command): void
    {
        $module = new Module();

        $createModule = $command->getCreateModule();

        $module->setName($createModule->getName());
        $module->setLanguage($createModule->getLanguage());
        $module->setCategory($createModule->getCategory());

        $this->entityManager->persist($module);
    }
}