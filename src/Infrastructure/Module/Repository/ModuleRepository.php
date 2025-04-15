<?php

declare(strict_types = 1);

namespace App\Infrastructure\Module\Repository;

use App\Application\Module\Model\ModuleViewModel;
use App\Application\Module\Repository\ModuleRepositoryInterface;
use App\Domain\Entity\Module;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class ModuleRepository implements ModuleRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getModuleViewModelsQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $select = sprintf(
            'NEW %s(m.id, m.name, m.language, m.category, COUNT(DISTINCT q.id), COUNT(DISTINCT v.id))', 
            ModuleViewModel::class
        );

        $queryBuilder
            ->select($select)
            ->from(Module::class, 'm')
            ->leftJoin('m.questions', 'q')
            ->leftJoin('m.videos', 'v')
            ->groupBy('m.id')
        ;

        return $queryBuilder;
    }
}