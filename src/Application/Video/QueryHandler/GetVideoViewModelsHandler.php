<?php

declare(strict_types = 1);

namespace App\Application\Video\QueryHandler;

use App\Application\Video\Query\GetVideoViewModels;
use App\Application\Video\Repository\VideoRepositoryInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetVideoViewModelsHandler
{
    public function __construct(
        private readonly VideoRepositoryInterface $videoRepository
    ) {
    }

    public function __invoke(GetVideoViewModels $query): QueryBuilder
    {
        return $this->videoRepository->getVideoViewModelsQueryBuilder($query->getModuleId());
    }
}