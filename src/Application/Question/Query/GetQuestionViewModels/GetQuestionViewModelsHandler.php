<?php

declare(strict_types=1);

namespace App\Application\Question\Query\GetQuestionViewModels;

use App\Application\Question\Repository\QuestionRepositoryInterface;
use App\Application\Shared\Bus\QueryBusHandlerInterface;
use Doctrine\ORM\QueryBuilder;

final class GetQuestionViewModelsHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly QuestionRepositoryInterface $questionRepository
    ) {}

    public function __invoke(GetQuestionViewModels $query): QueryBuilder
    {
        return $this->questionRepository->getQuestionViewModelsQueryBuilder($query->getModuleId());
    }
}
