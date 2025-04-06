<?php

declare(strict_types = 1);

namespace App\Application\Question\QueryHandler;

use App\Application\Answer\Model\AnswerModel;
use App\Application\Question\Model\QuestionModel;
use App\Application\Question\Query\GetQuestionModel;
use App\Domain\Entity\Question;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetQuestionModelHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(GetQuestionModel $query): QuestionModel
    {
        $questionid = $query->getQuestionId();

        $question = $this->entityManager->find(Question::class, $questionid);
        if ($question === null) {
            throw new NotFoundException(Question::class, ['id' => $questionid]);
        }

        $questionModel = new QuestionModel();
        $questionModel->setContent($question->getContent());

        foreach ($question->getAnswers() as $answer) {
            $answerModel = (new AnswerModel())
                ->setAnswerId($answer->getId())
                ->setContent($answer->getContent())
                ->setCorrect($answer->isCorrect())
            ;

            $questionModel->addAnswerModel($answerModel);
        }

        return $questionModel;
    }
}