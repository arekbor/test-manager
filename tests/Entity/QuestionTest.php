<?php 

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Answer;
use App\Entity\Question;
use PHPUnit\Framework\TestCase;

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
            ->addAnswer($answer3);

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
            ->addAnswer($answer3);

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
            ->addAnswer($answer3);

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
            ->addAnswer($answer3);

        $this->assertNull($answer1->getPosition());
        $this->assertNull($answer2->getPosition());
        $this->assertNull($answer3->getPosition());
    }
}