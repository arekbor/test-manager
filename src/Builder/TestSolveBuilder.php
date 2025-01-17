<?php 

declare(strict_types=1);

namespace App\Builder;

use App\Entity\Answer;
use App\Entity\Question;
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

        foreach($test->getModule()->getQuestions() as $question) {
            $testQuestionSolve = $this->buildTestQuestionSolve($question);

            $testAnswers = [];

            foreach($question->getAnswers() as $answer) {
                $testAnswerSolve = $this->buildTestAnswerSolve($answer);
                $testAnswers[] = $testAnswerSolve;
            }

            $testQuestionSolve->setTestAnswers($testAnswers);
            $testQuestions[] = $testQuestionSolve;
        }

        $testSolve->setTestQuestions($testQuestions);
        
        return $testSolve;
    }

    private function buildTestQuestionSolve(Question $question): TestQuestionSolve
    {
        $testQuestionSolve = new TestQuestionSolve();
        $testQuestionSolve->setQuestionId($question->getId());
        $testQuestionSolve->setContent($question->getContent());

        return $testQuestionSolve;
    }

    private function buildTestAnswerSolve(Answer $answer): TestAnswerSolve
    {
        $testAnswerSolve = new TestAnswerSolve();
        $testAnswerSolve->setAnswerId($answer->getId());
        $testAnswerSolve->setContent($answer->getContent());

        return $testAnswerSolve;
    }
}