<?php

declare(strict_types=1);

namespace App\Application\Test\Query\GetTestViewModels;

use App\Application\Shared\Bus\QueryBusHandlerInterface;
use App\Application\Test\Repository\TestRepositoryInterface;
use Doctrine\ORM\QueryBuilder;

final class GetTestViewModelsHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly TestRepositoryInterface $testRepository
    ) {}

    public function __invoke(GetTestViewModels $query): QueryBuilder
    {
        return $this->testRepository->getTestViewModelsQueryBuilder();
    }
}
