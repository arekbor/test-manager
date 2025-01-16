<?php 

declare(strict_types=1);

namespace App\Builder;

use App\Entity\Test;
use App\Model\TestAnswerSolve;
use App\Model\TestQuestionSolve;
use App\Model\TestSolve;

class TestSolveBuilder
{
    public function build(Test $test): TestSolve
    {
        $testSolve = new TestSolve();

        $testQuestions = [];

        foreach($test->getModule()->getQuestions() as $testQuestion) {
            $testQuestionSolve = new TestQuestionSolve();
            $testQuestionSolve->setQuestionId($testQuestion->getId());
            $testQuestionSolve->setContent($testQuestion->getContent());

            $testAnswers = [];

            foreach($testQuestion->getAnswers() as $testAnswer) {
                $testAnswerSolve = new TestAnswerSolve();
                $testAnswerSolve->setAnswerId($testAnswer->getId());
                $testAnswerSolve->setContent($testAnswer->getContent());

                $testAnswers[] = $testAnswerSolve;
            }

            $testQuestionSolve->setTestAnswers($testAnswers);

            $testQuestions[] = $testQuestionSolve;
        }

        $testSolve->setTestQuestions($testQuestions);

        return $testSolve;
    }
}