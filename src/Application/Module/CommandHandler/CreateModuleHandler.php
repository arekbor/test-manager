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

        $moduleModel = $command->getModuleModel();

        $module->setName($moduleModel->getName());
        $module->setLanguage($moduleModel->getLanguage());
        $module->setCategory($moduleModel->getCategory());

        $this->entityManager->persist($module);
    }
}