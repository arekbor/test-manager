<?php

declare(strict_types = 1);

namespace App\Application\Question\CommandHandler;

use App\Application\Question\Command\UpdateQuestion;
use App\Application\Question\Repository\QuestionRepositoryInterface;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Question;
use App\Domain\Exception\NotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateQuestionHandler
{
    public function __construct(
        private readonly QuestionRepositoryInterface $questionRepository
    ) {
    }

    public function __invoke(UpdateQuestion $command): void
    {
        $moduleId = $command->getModuleId();
        $questionId = $command->getQuestionId();

        $questionToUpdate = $this->questionRepository->getQuestionByQuestionAndModuleId($questionId, $moduleId);
        if ($questionToUpdate === null) {
            throw new NotFoundException(Question::class, [
                'questionId' => $questionId,
                'moduleId' => $moduleId,
            ]);
        }

        $questionModel = $command->getQuestionModel();
        $questionToUpdate->setContent($questionModel->getContent());

        foreach($questionToUpdate->getAnswers() as $answerToDelete) {
            if ($questionModel->getAnswerModelByAnswerId($answerToDelete->getId()) === null) {
                $questionToUpdate->removeAnswer($answerToDelete);
            }
        }

        foreach($questionModel->getAnswerModels() as $answerModel) {
            $answerToUpdate = $questionToUpdate->getAnswerById($answerModel->getAnswerId());
            if ($answerToUpdate !== null) {
                $answerToUpdate->setContent($answerModel->getContent());
                $answerToUpdate->setCorrect($answerModel->isCorrect());
            } else {
                $newAnswer = new Answer();
                $newAnswer->setContent($answerModel->getContent());
                $newAnswer->setCorrect($answerModel->isCorrect());

                $questionToUpdate->addAnswer($newAnswer);
            }
        }

        $questionToUpdate->updateAnswerPositions();
    }
}