<?php 

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Test;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class TestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Test::class);
    }

    public function findAllWithModules(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.module', 'module')
            ->addSelect('module')
            ->where('(t.testResult IS NOT NULL) OR (t.testResult IS NULL AND t.start IS NULL AND t.submission IS NULL)')
        ;
    }
}