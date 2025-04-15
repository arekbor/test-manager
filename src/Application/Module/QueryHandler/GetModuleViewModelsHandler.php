<?php

declare(strict_types = 1);

namespace App\Application\Module\QueryHandler;

use App\Application\Module\Query\GetModuleViewModels;
use App\Application\Module\Repository\ModuleRepositoryInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetModuleViewModelsHandler
{
    public function __construct(
        private readonly ModuleRepositoryInterface $moduleRepository
    ) {
    }

    public function __invoke(GetModuleViewModels $query): QueryBuilder
    {
        return $this->moduleRepository->getModuleViewModelsQueryBuilder();
    }
}