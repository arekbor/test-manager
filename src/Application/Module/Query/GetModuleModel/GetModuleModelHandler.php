<?php

declare(strict_types=1);

namespace App\Application\Module\Query\GetModuleModel;

use App\Application\Module\Model\ModuleModel;
use App\Application\Shared\Bus\QueryBusHandlerInterface;
use App\Domain\Entity\Module;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class GetModuleModelHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(GetModuleModel $query): ModuleModel
    {
        /**
         * @var Module|null $module
         */
        $module = $this->entityManager->find(Module::class, $query->getModuleId());
        if (!$module) {
            throw new NotFoundException(Module::class, ['id' => $query->getModuleId()]);
        }

        $moduleModel = new ModuleModel();
        $moduleModel->setName($module->getName());
        $moduleModel->setLanguage($module->getLanguage());
        $moduleModel->setCategory($module->getCategory());

        return $moduleModel;
    }
}
