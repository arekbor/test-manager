<?php

declare(strict_types = 1);

namespace App\Infrastructure\Video\Repository;

use App\Application\Video\Model\VideoViewModel;
use App\Application\Video\Repository\VideoRepositoryInterface;
use App\Domain\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\QueryBuilder;

final class VideoRepository implements VideoRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager        
    ) {
    }

    public function getVideoViewModelsQueryBuilder(Uuid $moduleId): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $select = sprintf(
            'NEW %s(v.id, m.id, v.originalName, v.mimeType, v.size)',
            VideoViewModel::class
        );

        $queryBuilder
            ->select($select)
            ->from(Video::class, 'v')
            ->innerJoin('v.modules', 'm')
            ->where('m.id = :module_id')
            ->setParameter('module_id', $moduleId)
            ->groupBy('v.id, m.id, v.originalName, v.mimeType, v.size');
        ;

        return $queryBuilder;
    }
}