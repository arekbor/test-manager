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
            
            if ($question->isAnswerCorrect($chosenAnswerIds)) {
                $score++;
            }
        }

        return $score;
    }
}