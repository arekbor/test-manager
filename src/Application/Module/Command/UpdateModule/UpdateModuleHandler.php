<?php

declare(strict_types=1);

namespace App\Application\Module\Command\UpdateModule;

use App\Domain\Entity\Module;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use App\Application\Module\Model\ModuleModel;
use App\Application\Shared\Bus\CommandBusHandlerInterface;

final class UpdateModuleHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(UpdateModule $command): void
    {
        $moduleId = $command->getModuleId();

        /**
         * @var Module $module
         */
        $module = $this->entityManager->find(Module::class, $moduleId);
        if ($module === null) {
            throw new NotFoundException(Module::class, ['id' => $moduleId]);
        }

        /**
         * @var ModuleModel $moduleModel
         */
        $moduleModel = $command->getModuleModel();

        $module->setName($moduleModel->getName());
        $module->setLanguage($moduleModel->getLanguage());
        $module->setCategory($moduleModel->getCategory());
    }
}
