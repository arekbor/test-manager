<?php

declare(strict_types=1);

namespace App\Application\Question\Command\UpdateQuestion;

use App\Application\Question\Repository\QuestionRepositoryInterface;
use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Question;
use App\Domain\Exception\NotFoundException;

final class UpdateQuestionHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly QuestionRepositoryInterface $questionRepository
    ) {}

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

        foreach ($questionToUpdate->getAnswers() as $answerToDelete) {
            if ($questionModel->getAnswerModelByAnswerId($answerToDelete->getId()) === null) {
                $questionToUpdate->removeAnswer($answerToDelete);
            }
        }

        foreach ($questionModel->getAnswerModels() as $answerModel) {
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
