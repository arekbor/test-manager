<?php

declare(strict_types = 1);

namespace App\Application\Module\CommandHandler;

use App\Application\Module\Command\UpdateModule;
use App\Domain\Entity\Module;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Application\Module\Model\UpdateModuleModel;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateModuleHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(UpdateModule $command): void
    {
        $moduleId = $command->getModuleId();

        /**
         * @var Module $module
         */
        $module = $this->entityManager->find(Module::class, $moduleId);
        if (!$module) {
            throw new NotFoundException(Module::class, ['id' => $moduleId]);
        }

        /**
         * @var UpdateModuleModel $updateModuleModel
         */
        $updateModuleModel = $command->getUpdateModuleModel();

        $module->setName($updateModuleModel->getName());
        $module->setLanguage($updateModuleModel->getLanguage());
        $module->setCategory($updateModuleModel->getCategory());

        $this->entityManager->persist($module);
    }
}