<?php

declare(strict_types=1);

namespace App\Application\Question\Command\DeleteQuestion;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\Question;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteQuestionHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(DeleteQuestion $command): void
    {
        $questionId = $command->getQuestionId();

        /**
         * @var Question|null $question
         */
        $question = $this->entityManager->find(Question::class, $questionId);
        if (!$question) {
            throw new NotFoundException(Question::class, ['id' => $questionId]);
        }

        $this->entityManager->remove($question);
    }
}
