<?php

declare(strict_types = 1);

namespace App\Infrastructure\Test\Repository;

use App\Application\Test\Model\TestViewModel;
use App\Application\Test\Repository\TestRepositoryInterface;
use App\Domain\Entity\Test;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class TestRepository implements TestRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getTestViewModelsQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $select = sprintf(
            'NEW %s(
                t.id, 
                m.id, 
                tr.id, 
                m.name, 
                m.language, 
                m.category, 
                t.email, 
                t.firstname, 
                t.lastname, 
                t.workplace, 
                t.dateOfBirth, 
                t.expiration, 
                t.start, 
                t.submission, 
                t.score
            )',
            TestViewModel::class
        );

        $queryBuilder
            ->select($select)
            ->from(Test::class, 't')
            ->leftJoin('t.module', 'm')
            ->leftJoin('t.testResult', 'tr')
        ;

        return $queryBuilder;
    }
}