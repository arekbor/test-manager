<?php

declare(strict_types = 1);

namespace App\Application\Question\CommandHandler;

use App\Application\Question\Command\DeleteQuestion;
use App\Domain\Entity\Question;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteQuestionHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(DeleteQuestion $command): void
    {
        $questionId = $command->getQuestionId();

        /**
         * @var Question $question
         */
        $question = $this->entityManager->find(Question::class, $questionId);
        if ($question === null) {
            throw new NotFoundException(Question::class, ['id' => $questionId]);
        }

        $this->entityManager->remove($question);
    }
}