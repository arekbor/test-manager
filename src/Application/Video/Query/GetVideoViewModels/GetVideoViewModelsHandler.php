<?php

declare(strict_types=1);

namespace App\Application\Video\Query\GetVideoViewModels;

use App\Application\Shared\Bus\QueryBusHandlerInterface;
use App\Application\Video\Repository\VideoRepositoryInterface;
use Doctrine\ORM\QueryBuilder;

final class GetVideoViewModelsHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly VideoRepositoryInterface $videoRepository
    ) {}

    public function __invoke(GetVideoViewModels $query): QueryBuilder
    {
        return $this->videoRepository->getVideoViewModelsQueryBuilder($query->getModuleId());
    }
}
