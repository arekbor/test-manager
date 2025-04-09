<?php

declare(strict_types = 1);

namespace App\Application\Test\QueryHandler;

use App\Application\Test\Model\DataForTestSolve;
use App\Application\Test\Model\TestAnswerSolve;
use App\Application\Test\Model\TestQuestionSolve;
use App\Application\Test\Model\TestSolve;
use App\Application\Test\Query\GetDataForTestSolve;
use App\Application\Video\Model\TestVideo;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetDataForTestSolveHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(GetDataForTestSolve $query): DataForTestSolve
    {
        $testId = $query->getTestId();

        /**
         * @var Test $test
         */
        $test = $this->entityManager->find(Test::class, $testId);
        if ($test === null) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }

        $dataForTestSolve = new DataForTestSolve();

        $testModule = $test->getModule();

        $dataForTestSolve->setTestId($test->getId());
        $dataForTestSolve->setTestCategory($testModule->getCategory());

        $testSolve = new TestSolve();

        $questions = $testModule->getQuestions();

        foreach($questions as $question) {
            $testQuestionSolve = new TestQuestionSolve();
            $testQuestionSolve->setQuestionId($question->getId());
            $testQuestionSolve->setContent($question->getContent());

            foreach($question->getAnswers() as $answer) {
                $testAnswerSolve = new TestAnswerSolve();
                $testAnswerSolve->setAnswerId($answer->getId());
                $testAnswerSolve->setContent($answer->getContent());

                $testQuestionSolve->addTestAnswerSolve($testAnswerSolve);
            }

            $testSolve->addTestQuestionSolve($testQuestionSolve);
        }

        $dataForTestSolve->setTestSolve($testSolve);

        $videos = $testModule->getVideos();

        /**
         * @var TestVideo[] $testVideos
         */
        $testVideos = [];

        foreach($videos as $video) {
            $testVideos[] = (new TestVideo)->setVideoId($video->getId());
        }

        $dataForTestSolve->setTestVideos($testVideos);

        return $dataForTestSolve;
    }
}