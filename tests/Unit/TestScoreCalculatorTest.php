<?php

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\Application\Test\Service\TestScoreCalculator;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use App\Domain\Entity\Test;
use App\Application\Test\Model\TestAnswerSolve;
use App\Application\Test\Model\TestQuestionSolve;
use App\Domain\Model\TestSolve;
use App\Tests\EntityHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class TestScoreCalculatorTest extends TestCase
{
    #[Test]
    public function testCalculatorAwardsCorrectNumberOfPoints(): void
    {
        //Arrange
        $answersOfQuestion1 = [
            $this->createAnswer($question_1_answer_1_id = Uuid::v4()),
            $this->createAnswer($question_1_answer_2_id = Uuid::v4()),
            $this->createAnswer($question_1_answer_3_id = Uuid::v4(), true),
            $this->createAnswer($question_1_answer_4_id = Uuid::v4())
        ];

        $answersOfQuestion2 = [
            $this->createAnswer($question_2_answer_1_id = Uuid::v4()),
            $this->createAnswer($question_2_answer_2_id = Uuid::v4(), true),
            $this->createAnswer($question_2_answer_3_id = Uuid::v4()),
            $this->createAnswer($question_2_answer_4_id = Uuid::v4(), true)
        ];

        $answersOfQuestion3 = [
            $this->createAnswer($question_3_answer_1_id = Uuid::v4(), true),
            $this->createAnswer($question_3_answer_2_id = Uuid::v4()),
            $this->createAnswer($question_3_answer_3_id = Uuid::v4()),
            $this->createAnswer($question_3_answer_4_id = Uuid::v4())
        ];

        $answersOfQuestion4 = [
            $this->createAnswer($question_4_answer_1_id = Uuid::v4(), true),
            $this->createAnswer($question_4_answer_2_id = Uuid::v4(), true),
            $this->createAnswer($question_4_answer_3_id = Uuid::v4(), true),
            $this->createAnswer($question_4_answer_4_id = Uuid::v4())
        ];

        $questions = [
            $this->createQuestion($question1Id = Uuid::v4(), ...$answersOfQuestion1),
            $this->createQuestion($question2Id = Uuid::v4(), ...$answersOfQuestion2),
            $this->createQuestion($question3Id = Uuid::v4(), ...$answersOfQuestion3),
            $this->createQuestion($question4Id = Uuid::v4(), ...$answersOfQuestion4),
        ];

        $testAnswerSolvesofQuestion1 = [
            $this->createTestAnswerSolve($question_1_answer_1_id),
            $this->createTestAnswerSolve($question_1_answer_2_id),
            $this->createTestAnswerSolve($question_1_answer_3_id, true),
            $this->createTestAnswerSolve($question_1_answer_4_id),
        ];

        $testAnswerSolvesofQuestion2 = [
            $this->createTestAnswerSolve($question_2_answer_1_id),
            $this->createTestAnswerSolve($question_2_answer_2_id, true),
            $this->createTestAnswerSolve($question_2_answer_3_id),
            $this->createTestAnswerSolve($question_2_answer_4_id, true),
        ];

        $testAnswerSolvesofQuestion3 = [
            $this->createTestAnswerSolve($question_3_answer_1_id),
            $this->createTestAnswerSolve($question_3_answer_2_id, true),
            $this->createTestAnswerSolve($question_3_answer_3_id),
            $this->createTestAnswerSolve($question_3_answer_4_id),
        ];

        $testAnswerSolvesofQuestion4 = [
            $this->createTestAnswerSolve($question_4_answer_1_id, true),
            $this->createTestAnswerSolve($question_4_answer_2_id, true),
            $this->createTestAnswerSolve($question_4_answer_3_id, true),
            $this->createTestAnswerSolve($question_4_answer_4_id),
        ];

        $testQuestionSolves = [
            $this->createTestQuestionSolve($question1Id, $testAnswerSolvesofQuestion1),
            $this->createTestQuestionSolve($question2Id, $testAnswerSolvesofQuestion2),
            $this->createTestQuestionSolve($question3Id, $testAnswerSolvesofQuestion3),
            $this->createTestQuestionSolve($question4Id, $testAnswerSolvesofQuestion4),
        ];

        $test = $this->createTest(...$questions);
        $testSolve = $this->createTestSolve($testQuestionSolves);

        //Act
        $testScoreCalculator = new TestScoreCalculator();
        $score = $testScoreCalculator->calculate($testSolve, $test);

        //Assert
        $this->assertEquals(3, $score);
    }

    #[Test]
    public function testCalculatorDoesNotAwardPointForWrongAnswer(): void
    {
        //Arrange
        $answers = [
            $this->createAnswer($answer1 = Uuid::v4()),
            $this->createAnswer($answer2 = Uuid::v4()),
            $this->createAnswer($answer3 = Uuid::v4(), true),
            $this->createAnswer($answer4 = Uuid::v4())
        ];

        $questions = [
            $this->createQuestion($questionId = Uuid::v4(), ...$answers),
        ];

        $testAnswerSolves = [
            $this->createTestAnswerSolve($answer1, true),
            $this->createTestAnswerSolve($answer2),
            $this->createTestAnswerSolve($answer3),
            $this->createTestAnswerSolve($answer4),
        ];

        $testQuestionSolves = [
            $this->createTestQuestionSolve($questionId, $testAnswerSolves),
        ];

        $test = $this->createTest(...$questions);
        $testSolve = $this->createTestSolve($testQuestionSolves);

        //Act
        $testScoreCalculator = new TestScoreCalculator();
        $score = $testScoreCalculator->calculate($testSolve, $test);

        //Assert
        $this->assertEquals(0, $score);
    }

    #[Test]
    public function testCalculatorDoesNotAwardPointForUnselectedAnswer(): void
    {
        //Arrange
        $answers = [
            $this->createAnswer($answer1 = Uuid::v4()),
            $this->createAnswer($answer2 = Uuid::v4()),
            $this->createAnswer($answer3 = Uuid::v4(), true),
            $this->createAnswer($answer4 = Uuid::v4())
        ];

        $questions = [
            $this->createQuestion($questionId = Uuid::v4(), ...$answers),
        ];

        $testAnswerSolves = [
            $this->createTestAnswerSolve($answer1),
            $this->createTestAnswerSolve($answer2),
            $this->createTestAnswerSolve($answer3),
            $this->createTestAnswerSolve($answer4),
        ];

        $testQuestionSolves = [
            $this->createTestQuestionSolve($questionId, $testAnswerSolves),
        ];

        $test = $this->createTest(...$questions);
        $testSolve = $this->createTestSolve($testQuestionSolves);

        //Act
        $testScoreCalculator = new TestScoreCalculator();
        $score = $testScoreCalculator->calculate($testSolve, $test);

        //Assert
        $this->assertEquals(0, $score);
    }

    #[Test]
    public function testCalculatorDoesNotAwardPointWhenNoAnswersSelected(): void
    {
        //Arrange
        $answers = [
            $this->createAnswer(Uuid::v4()),
            $this->createAnswer(Uuid::v4()),
            $this->createAnswer(Uuid::v4(), true),
            $this->createAnswer(Uuid::v4())
        ];

        $questions = [
            $this->createQuestion(Uuid::v4(), ...$answers),
        ];

        $test = $this->createTest(...$questions);
        $testSolve = $this->createTestSolve([]);

        //Act
        $testScoreCalculator = new TestScoreCalculator();
        $score = $testScoreCalculator->calculate($testSolve, $test);

        //Assert
        $this->assertEquals(0, $score);
    }

    private function createAnswer(Uuid $id, bool $correct = false): Answer
    {
        $answer = new Answer();
        EntityHelper::setId($id, Answer::class, $answer);
        $answer->setCorrect($correct);

        return $answer;
    }

    private function createQuestion(Uuid $id, Answer ...$answers): Question
    {
        $question = new Question();
        EntityHelper::setId($id, Question::class, $question);
        
        /**
         * @var Answer $answer
         */
        foreach($answers as $answer) {
            $question->addAnswer($answer);
        }

        return $question;
    }

    private function createTest(Question ...$questions): Test
    {
        $module = new Module();
        /**
         * @var Question $question
         */
        foreach($questions as $question) {
            $module->addQuestion($question);
        }

        $test = new Test();
        $test->setModule($module);

        return $test;
    }

    private function createTestAnswerSolve(Uuid $answerId, bool $chosen = false): TestAnswerSolve
    {
        $testAnswerSolve = new TestAnswerSolve();
        $testAnswerSolve->setAnswerId($answerId);
        $testAnswerSolve->setChosen($chosen);

        return $testAnswerSolve;
    }

    private function createTestQuestionSolve(Uuid $questionId, array $testAnswerSolves): TestQuestionSolve
    {
        $testQuestionSolve = new TestQuestionSolve();
        $testQuestionSolve->setQuestionId($questionId);
        $testQuestionSolve->setTestAnswers($testAnswerSolves);

        return $testQuestionSolve;
    }

    private function createTestSolve(array $testQuestionSolves): TestSolve
    {
        $testSolve = new TestSolve();
        $testSolve->setTestQuestions($testQuestionSolves);

        return $testSolve;
    }
}