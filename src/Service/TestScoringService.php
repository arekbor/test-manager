<?php 

declare(strict_types=1);

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Model\TestAnswerSolve;
use App\Model\TestQuestionSolve;
use App\Model\TestSolve;
use Doctrine\Common\Collections\Collection;

class TestScoringService
{
    public function calculate(Collection $questions, TestSolve $testSolve): int
    {
        $score = 0;

        foreach($testSolve->getTestQuestions() as $testQuestionSolve) {
            $question = $this->findMatchingQuestion($questions, $testQuestionSolve);
            if (!isset($question)) {
                continue;
            }

            $testAnswers = $testQuestionSolve->getTestAnswers();
            $chosenAnswerIds = $this->extractChosenAnswerIds($testAnswers);
            if (empty($chosenAnswerIds)) {
                continue;
            }

            $answers = $question->getAnswers()->toArray();
            $correctAnswerIds = $this->extractCorrectAnswerIds($answers);

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

    private function findMatchingQuestion(Collection $questions, TestQuestionSolve $testQuestionSolve): ?Question
    {
        return $questions->filter(fn(Question $q) => $q->getId() === $testQuestionSolve->getQuestionId())->first() ?: null;
    }

    private function extractChosenAnswerIds(array $testAnswers): array
    {
        $chosenAnswers = array_filter(
            $testAnswers, 
            fn(TestAnswerSolve $testAnswerSolve) => $testAnswerSolve->isChosen()
        );

        $chosenAnswerIds = array_map(
            fn(TestAnswerSolve $testAnswerSolve) => $testAnswerSolve->getAnswerId()->toRfc4122(), 
            $chosenAnswers)
        ;

        return array_values($chosenAnswerIds);
    }

    private function extractCorrectAnswerIds(array $answers): array
    {
        $correctAnswers = array_filter(
            $answers, 
            fn(Answer $answer) => $answer->isCorrect()
        );

        $correctAnswerIds = array_map(
            fn(Answer $answer) => $answer->getId()->toRfc4122(), 
            $correctAnswers
        );

        return array_values($correctAnswerIds);
    }
}