<?php

declare(strict_types = 1);

namespace App\Application\Question\QueryHandler;

use App\Application\Question\Query\GetQuestionViewModels;
use App\Application\Question\Repository\QuestionRepositoryInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetQuestionViewModelsHandler
{
    public function __construct(
        private readonly QuestionRepositoryInterface $questionRepository
    ) {
    }

    public function __invoke(GetQuestionViewModels $query): QueryBuilder
    {
        return $this->questionRepository->getQuestionViewModelsQueryBuilder($query->getModuleId());
    }
}