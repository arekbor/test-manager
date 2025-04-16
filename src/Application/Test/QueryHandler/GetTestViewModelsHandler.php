<?php

declare(strict_types = 1);

namespace App\Application\Test\QueryHandler;

use App\Application\Test\Query\GetTestViewModels;
use App\Application\Test\Repository\TestRepositoryInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetTestViewModelsHandler
{
    public function __construct(
        private readonly TestRepositoryInterface $testRepository
    ) {
    }

    public function __invoke(GetTestViewModels $query): QueryBuilder
    {
        return $this->testRepository->getTestViewModelsQueryBuilder();
    }
}