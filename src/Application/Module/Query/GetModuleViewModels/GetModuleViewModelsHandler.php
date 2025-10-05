<?php

declare(strict_types=1);

namespace App\Application\Module\Query\GetModuleViewModels;

use App\Application\Module\Repository\ModuleRepositoryInterface;
use App\Application\Shared\Bus\QueryBusHandlerInterface;
use Doctrine\ORM\QueryBuilder;

final class GetModuleViewModelsHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly ModuleRepositoryInterface $moduleRepository
    ) {}

    public function __invoke(GetModuleViewModels $query): QueryBuilder
    {
        return $this->moduleRepository->getModuleViewModelsQueryBuilder();
    }
}
