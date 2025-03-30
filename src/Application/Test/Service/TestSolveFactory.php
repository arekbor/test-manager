<?php

declare(strict_types = 1);

namespace App\Application\Test\Service;

use App\Application\Test\Model\TestAnswerSolve;
use App\Application\Test\Model\TestQuestionSolve;
use App\Application\Test\Model\TestSolve;
use App\Domain\Entity\Module;

final class TestSolveFactory
{
    public static function createFromModule(Module $module): TestSolve
    {
        $testSolve = new TestSolve();

        /**
         * @var TestQuestionSolve[] $testQuestionSolves
         */
        $testQuestionSolves = [];

        $questions = $module->getQuestions();

        foreach($questions as $question) {
            $testQuestionSolve = new TestQuestionSolve();
            $testQuestionSolve->setQuestionId($question->getId());
            $testQuestionSolve->setContent($question->getContent());

            /**
             * @var TestAnswerSolve[] $testAnswerSolves
             */
            $testAnswerSolves = [];

            foreach($question->getAnswers() as $answer) {
                $testAnswerSolve = new TestAnswerSolve();
                $testAnswerSolve->setAnswerId($answer->getId());
                $testAnswerSolve->setContent($answer->getContent());

                $testAnswerSolves[] = $testAnswerSolve;
            }

            $testQuestionSolve->setTestAnswers($testAnswerSolves);

            $testQuestionSolves[] = $testQuestionSolve;
        }

        $testSolve->setTestQuestions($testQuestionSolves);

        return $testSolve;
    }
}