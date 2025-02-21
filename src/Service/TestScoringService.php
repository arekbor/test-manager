<?php 

declare(strict_types=1);

namespace App\Service;

use App\Entity\Test;
use App\Model\TestSolve;

class TestScoringService
{
    public function calculate(Test $test, TestSolve $testSolve): int
    {
        $score = 0;

        foreach($testSolve->getTestQuestions() as $testQuestionSolve) {
            $testQuestionSolveId = $testQuestionSolve->getQuestionId();
            $question = $test->getModule()->findQuestionById($testQuestionSolveId);
            if (!isset($question)) {
                continue;
            }

            $chosenAnswerIds = $testQuestionSolve->extractChosenAnswerIds();
            if (empty($chosenAnswerIds)) {
                continue;
            }

            $correctAnswerIds = $question->extractCorrectAnswerIds();

            if (count($correctAnswerIds) === 1) {
                // Standard question - award 1 point for a correct answer
                if ($chosenAnswerIds[0] === $correctAnswerIds[0]) {
                    $score++;
                }
            } else {
                // Multiple choice question - award a point only for all correct answers
                $diff = array_diff($chosenAnswerIds, $correctAnswerIds);
                if (empty($diff) && count($chosenAnswerIds) === count($correctAnswerIds)) {
                    $score++;
                }
            }
        }

        return $score;
    }
}