<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findByModuleId(int $moduleId)
    {
        return $this
            ->createQueryBuilder('q')
            ->innerJoin('q.modules', 'm')
            ->where('m.id = :module_id')
            ->setParameter('module_id', $moduleId);
    }
}
