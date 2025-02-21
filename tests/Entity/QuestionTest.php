<?php 

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Answer;
use App\Entity\Question;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class QuestionTest extends TestCase
{
    public function testUpdateAnswerPositionsAssignsSequentialPositions(): void
    {
        $answer1 = (new Answer())->setContent('Answer 1');
        $answer2 = (new Answer())->setContent('Answer 2');
        $answer3 = (new Answer())->setContent('Answer 3');

        $question = (new Question())
            ->addAnswer($answer1)
            ->addAnswer($answer2)
            ->addAnswer($answer3)
        ;

        $question->updateAnswerPositions();

        $this->assertEquals(1, $answer1->getPosition());
        $this->assertEquals(2, $answer2->getPosition());
        $this->assertEquals(3, $answer3->getPosition());
    }

    public function testUpdateAnswerPositionsAfterRemovingAnAnswer(): void
    {
        $answer1 = (new Answer())->setContent('Answer 1');
        $answer2 = (new Answer())->setContent('Answer 2');
        $answer3 = (new Answer())->setContent('Answer 3');

        $question = (new Question())
            ->addAnswer($answer1)
            ->addAnswer($answer2)
            ->addAnswer($answer3)
        ;

        $question->removeAnswer($answer2);
        $question->updateAnswerPositions();

        $this->assertEquals(1, $answer1->getPosition());
        $this->assertEquals(2, $answer3->getPosition());
    }

    public function testUpdateAnswerPositionsWithReorderedAnswers(): void
    {
        $answer1 = (new Answer())->setContent('Answer 1')->setPosition(3);
        $answer2 = (new Answer())->setContent('Answer 2')->setPosition(1);
        $answer3 = (new Answer())->setContent('Answer 3')->setPosition(2);

        $question = (new Question())
            ->addAnswer($answer1)
            ->addAnswer($answer2)
            ->addAnswer($answer3)
        ;

        $question->updateAnswerPositions();

        $this->assertEquals(1, $answer1->getPosition());
        $this->assertEquals(2, $answer2->getPosition());
        $this->assertEquals(3, $answer3->getPosition());
    }

    public function testAnswerPositionsAreInitiallyNull(): void
    {
        $answer1 = (new Answer())->setContent('Answer 1');
        $answer2 = (new Answer())->setContent('Answer 2');
        $answer3 = (new Answer())->setContent('Answer 3');

        (new Question())
            ->addAnswer($answer1)
            ->addAnswer($answer2)
            ->addAnswer($answer3)
        ;

        $this->assertNull($answer1->getPosition());
        $this->assertNull($answer2->getPosition());
        $this->assertNull($answer3->getPosition());
    }

    public function testExtractCorrectAnswerIdsReturnsOnlyCorrectAnswers(): void
    {
        $answer1 = $this->createMock(Answer::class);
        $answer1Id = Uuid::v7();
        $answer1->method('getId')->willReturn($answer1Id);
        $answer1->method('isCorrect')->willReturn(false);

        $answer2 = $this->createMock(Answer::class);
        $answer2Id = Uuid::v7();
        $answer2->method('getId')->willReturn($answer2Id);
        $answer2->method('isCorrect')->willReturn(true);

        $answer3 = $this->createMock(Answer::class);
        $answer3Id = Uuid::v7();
        $answer3->method('getId')->willReturn($answer3Id);
        $answer3->method('isCorrect')->willReturn(false);

        $answer4 = $this->createMock(Answer::class);
        $answer4Id = Uuid::v7();
        $answer4->method('getId')->willReturn($answer4Id);
        $answer4->method('isCorrect')->willReturn(true);

        $question = new Question();
        $question
            ->addAnswer($answer1)
            ->addAnswer($answer2)
            ->addAnswer($answer3)
            ->addAnswer($answer4)
        ;

        $extractedQuestionIds = $question->extractCorrectAnswerIds();
        $this->assertEquals([$answer2Id, $answer4Id], $extractedQuestionIds);
    }

    public function testExtractCorrectAnswerIdsWithoutCorrectAnswersReturnsEmptyArray(): void
    {
        $answer1 = $this->createMock(Answer::class);
        $answer1Id = Uuid::v7();
        $answer1->method('getId')->willReturn($answer1Id);
        $answer1->method('isCorrect')->willReturn(false);

        $answer2 = $this->createMock(Answer::class);
        $answer2Id = Uuid::v7();
        $answer2->method('getId')->willReturn($answer2Id);
        $answer2->method('isCorrect')->willReturn(false);

        $question = new Question();
        $question
            ->addAnswer($answer1)
            ->addAnswer($answer2)
        ;

        $extractedQuestionIds = $question->extractCorrectAnswerIds();
        $this->assertEquals([], $extractedQuestionIds);
    }

    public function testExtractCorrectAnswerIdsWithoutAnyAnswersReturnsEmptyArray(): void
    {
        $question = new Question();

        $this->assertEquals([], $question->extractCorrectAnswerIds());
    }
}