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
            ->createQueryBuilder('q') // alias dla encji Question
            ->innerJoin('q.modules', 'm') // dołączamy relację many-to-many między Question a Module
            ->where('m.id = :module_id') // filtrujemy po moduleId
            ->setParameter('module_id', $moduleId);
    }
}
