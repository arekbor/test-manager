<?php

declare(strict_types=1);

namespace App\Application\Question\Command\AddQuestionToModule;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\Module;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Question;

final class AddQuestionToModuleHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(AddQuestionToModule $command): void
    {
        $moduleId = $command->getModuleId();

        $module = $this->entityManager->find(Module::class, $moduleId);
        if ($module === null) {
            throw new NotFoundException(Module::class, ['id' => $moduleId]);
        }

        $questionModel = $command->getQuestionModel();

        $question = new Question();
        $question->setContent($questionModel->getContent());

        foreach ($questionModel->getAnswerModels() as $answerModel) {
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
