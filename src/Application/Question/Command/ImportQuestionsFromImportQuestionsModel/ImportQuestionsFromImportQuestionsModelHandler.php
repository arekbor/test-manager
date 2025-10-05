<?php

declare(strict_types=1);

namespace App\Application\Question\Command\ImportQuestionsFromImportQuestionsModel;

use App\Application\Answer\Model\AnswerModel;
use App\Application\Question\Model\QuestionModel;
use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class ImportQuestionsFromImportQuestionsModelHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(ImportQuestionsFromImportQuestionsModel $command): void
    {
        $moduleId = $command->getModuleId();

        /**
         * @var Module|null $module
         */
        $module = $this->entityManager->find(Module::class, $moduleId);
        if (!$module) {
            throw new NotFoundException(Module::class, ['id' => $moduleId]);
        }

        $importQuestionsModel = $command->getImportQuestionsModel();

        /**
         * @var QuestionModel[] $questionModels
         */
        $questionModels = $importQuestionsModel->getQuestionModels();

        foreach ($questionModels as $questionModel) {
            $question = new Question();
            $question->setContent($questionModel->getContent());

            /**
             * @var AnswerModel[] $answerModels
             */
            $answerModels = $questionModel->getAnswerModels();

            foreach ($answerModels as $answerModel) {
                $answer = new Answer();
                $answer->setContent($answerModel->getContent());
                $answer->setCorrect($answerModel->isCorrect());

                $question->addAnswer($answer);
            }

            $question->updateAnswerPositions();
            $this->entityManager->persist($question);

            $module->addQuestion($question);
        }
    }
}
