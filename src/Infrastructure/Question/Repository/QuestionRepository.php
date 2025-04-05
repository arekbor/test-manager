<?php

declare(strict_types = 1);

namespace App\Infrastructure\Question\Repository;

use App\Application\Question\Repository\QuestionRepositoryInterface;
use App\Domain\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

final class QuestionRepository implements QuestionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getQuestionByQuestionAndModuleId(Uuid $questionId, Uuid $moduleId): ?Question
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('q')
            ->from(Question::class, 'q')
            ->join('q.modules', 'm')
            ->where('q.id = :questionId')
            ->andWhere('m.id = :moduleId')
            ->setParameter('questionId', $questionId, UuidType::NAME)
            ->setParameter('moduleId', $moduleId, UuidType::NAME)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}