<?php

declare(strict_types = 1);

namespace App\Application\Test\Service;

use App\Domain\Entity\Test;
use App\Application\Test\Model\TestSolve;
use App\Application\Test\Model\TestQuestionSolve;

final class TestScoreCalculator
{
    public function calculate(TestSolve $testSolve, Test $test): int
    {
        $score = 0;

        /**
         * @var TestQuestionSolve $testQuestionSolve
         */
        foreach($testSolve->getTestQuestionSolves() as $testQuestionSolve) {
            $questionId = $testQuestionSolve->getQuestionId();

            $question = $test->getModule()->findQuestionById($questionId);
            if (!$question) {
                continue;
            }

            $chosenAnswerIds = $testQuestionSolve->extractChosenAnswerIds();
            if (!empty($chosenAnswerIds) && $question->chosenAnswersCorrect($chosenAnswerIds)) {
                $score++;
            }
        }

        return $score;
    }
}