<?php 

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Answer;
use App\Domain\Entity\Question;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('answersProvider')]
    public function testisAnswerCorrectShouldReturnTrue(array $correctAnswerIds, array $chosenAnswerIds, bool $expectedResult): void
    {
        $question = new Question();

        foreach($correctAnswerIds as $correctAnswerId) {
            $answer = $this->createMock(Answer::class);
            $answer->method('getId')->willReturn($correctAnswerId);
            $answer->method('isCorrect')->willReturn(true);

            $question->addAnswer($answer);
        }

        $result = $question->chosenAnswersCorrect($chosenAnswerIds);

        $this->assertEquals($expectedResult, $result);
    }

    public static function answersProvider(): array
    {
        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();
        $uuid3 = Uuid::v7();
        $uuid4 = Uuid::v7();
        $uuid5 = Uuid::v7();
        $uuid6 = Uuid::v7();

        return [
            'all_answers_correct' => [
                'correctAnswerIds' => [$uuid1, $uuid2],
                'chosenAnswerIds' => [$uuid1->toRfc4122(), $uuid2->toRfc4122()],
                'expectedResult' => true,
            ],
            'all_answers_correct_reversed_order' => [
                'correctAnswerIds' => [$uuid1, $uuid2],
                'chosenAnswerIds' => [$uuid2->toRfc4122(), $uuid1->toRfc4122()],
                'expectedResult' => true,
            ],
            'multiple_answers_correct' => [
                'correctAnswerIds' => [$uuid3, $uuid1, $uuid5, $uuid4],
                'chosenAnswerIds' => [$uuid3->toRfc4122(), $uuid1->toRfc4122(), $uuid5->toRfc4122(), $uuid4->toRfc4122()],
                'expectedResult' => true,
            ],
            'single_correct_answer' => [
                'correctAnswerIds' => [$uuid3],
                'chosenAnswerIds' => [$uuid3->toRfc4122()],
                'expectedResult' => true,
            ],
            'partial_correct_answers' => [
                'correctAnswerIds' => [$uuid1, $uuid2],
                'chosenAnswerIds' => [$uuid1->toRfc4122()],
                'expectedResult' => false,
            ],
            'completely_incorrect_answers' => [
                'correctAnswerIds' => [$uuid1, $uuid2],
                'chosenAnswerIds' => [$uuid3->toRfc4122()],
                'expectedResult' => false,
            ],
            'single_incorrect_answer' => [
                'correctAnswerIds' => [$uuid2],
                'chosenAnswerIds' => [$uuid1->toRfc4122()],
                'expectedResult' => false,
            ],
            'more_answers_chosen_than_correct' => [
                'correctAnswerIds' => [$uuid1, $uuid2],
                'chosenAnswerIds' => [$uuid1->toRfc4122(), $uuid2->toRfc4122(), $uuid3->toRfc4122()],
                'expectedResult' => false,
            ],
            'mix_of_correct_and_incorrect_answers' => [
                'correctAnswerIds' => [$uuid4, $uuid5],
                'chosenAnswerIds' => [$uuid4->toRfc4122(), $uuid6->toRfc4122()],
                'expectedResult' => false,
            ],
            'no_answers_chosen' => [
                'correctAnswerIds' => [$uuid5, $uuid4],
                'chosenAnswerIds' => [],
                'expectedResult' => false,
            ],
            'empty_correct_and_chosen_answers' => [
                'correctAnswerIds' => [],
                'chosenAnswerIds' => [],
                'expectedResult' => false,
            ],
            'no_correct_answers_but_something_chosen' => [
                'correctAnswerIds' => [],
                'chosenAnswerIds' => [$uuid1->toRfc4122(), $uuid2->toRfc4122()],
                'expectedResult' => false,
            ],
        ];
    }
}