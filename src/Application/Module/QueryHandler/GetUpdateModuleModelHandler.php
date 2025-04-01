<?php

declare(strict_types = 1);

namespace App\Application\Module\QueryHandler;

use App\Application\Module\Model\UpdateModuleModel;
use App\Application\Module\Query\GetUpdateModuleModel;
use App\Domain\Entity\Module;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetUpdateModuleModelHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(GetUpdateModuleModel $query): UpdateModuleModel
    {
        /**
         * @var Module $module
         */
        $module = $this->entityManager->find(Module::class, $query->getModuleId());
        if (!$module) {
            throw new NotFoundException(Module::class, ['id' => $query->getModuleId()]);
        }

        $updateModuleModel = new UpdateModuleModel();
        $updateModuleModel->setName($module->getName());
        $updateModuleModel->setLanguage($module->getLanguage());
        $updateModuleModel->setCategory($module->getCategory());
        
        return $updateModuleModel;
    }
}