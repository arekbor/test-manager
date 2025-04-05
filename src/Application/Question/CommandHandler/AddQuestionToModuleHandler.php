<?php

declare(strict_types = 1);

namespace App\Application\Question\CommandHandler;

use App\Application\Question\Command\AddQuestionToModule;
use App\Domain\Entity\Module;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Question;

#[AsMessageHandler(bus: 'command.bus')]
final class AddQuestionToModuleHandler
{   
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(AddQuestionToModule $command): void
    {
        $moduleId = $command->getModuleId();

        $module = $this->entityManager->find(Module::class, $moduleId);
        if (!$module) {
            throw new NotFoundException(Module::class, ['id' => $moduleId]);
        }

        $questionModel = $command->getQuestionModel();

        $question = new Question();
        $question->setContent($questionModel->getContent());

        foreach($questionModel->getAnswerModels() as $answerModel) {
            $answer = new Answer();
            $answer->setContent($answerModel->getContent());
            $answer->setCorrect($answerModel->isCorrect());

            $question->addAnswer($answer);
        }

        $question->updateAnswerPositions();

        $question->addModule($module);

        $this->entityManager->persist($question);
    }
}