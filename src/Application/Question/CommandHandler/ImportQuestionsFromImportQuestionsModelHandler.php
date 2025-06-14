<?php

declare(strict_types=1);

namespace App\Application\Question\CommandHandler;

use App\Application\Answer\Model\AnswerModel;
use App\Application\Question\Command\ImportQuestionsFromImportQuestionsModel;
use App\Application\Question\Model\QuestionModel;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class ImportQuestionsFromImportQuestionsModelHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(ImportQuestionsFromImportQuestionsModel $command): void
    {
        $moduleId = $command->getModuleId();

        /**
         * @var Module $module
         */
        $module = $this->entityManager->find(Module::class, $moduleId);
        if ($module === null) {
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
