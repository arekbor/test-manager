<?php

declare(strict_types = 1);

namespace App\Application\Question\Repository;

use App\Domain\Entity\Question;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

interface QuestionRepositoryInterface
{
    public function getQuestionByQuestionAndModuleId(Uuid $questionId, Uuid $moduleId): ?Question;
    public function getQuestionViewModelsQueryBuilder(Uuid $moduleId): QueryBuilder;
}