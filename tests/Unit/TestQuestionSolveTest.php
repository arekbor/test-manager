<?php 

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\Domain\Model\TestAnswerSolve;
use App\Domain\Model\TestQuestionSolve;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class TestQuestionSolveTest extends TestCase
{
    #[Test]
    public function testExtractsOnlyChosenAnswerIds(): void
    {
        //Arrange
        $testAnswerSolve1 = $this->createMock(TestAnswerSolve::class);
        $testAnswerSolve1Id = Uuid::v7();
        $testAnswerSolve1->method('getAnswerId')->willReturn($testAnswerSolve1Id);
        $testAnswerSolve1->method('isChosen')->willReturn(true);

        $testAnswerSolve2 = $this->createMock(TestAnswerSolve::class);
        $testAnswerSolve2Id = Uuid::v7();
        $testAnswerSolve2->method('getAnswerId')->willReturn($testAnswerSolve2Id);
        $testAnswerSolve2->method('isChosen')->willReturn(true);

        $testAnswerSolve3 = $this->createMock(TestAnswerSolve::class);
        $testAnswerSolve3Id = Uuid::v7();
        $testAnswerSolve3->method('getAnswerId')->willReturn($testAnswerSolve3Id);
        $testAnswerSolve3->method('isChosen')->willReturn(false);

        $testAnswerSolve4 = $this->createMock(TestAnswerSolve::class);
        $testAnswerSolve4Id = Uuid::v7();
        $testAnswerSolve4->method('getAnswerId')->willReturn($testAnswerSolve4Id);
        $testAnswerSolve4->method('isChosen')->willReturn(true);

        $testQuestionSolve = new TestQuestionSolve();

        $testQuestionSolve->setTestAnswers([$testAnswerSolve1, $testAnswerSolve2, $testAnswerSolve3, $testAnswerSolve4]);

        //Act
        $extractedAnswerIds = $testQuestionSolve->extractChosenAnswerIds();

        //Assert
        $this->assertEquals([$testAnswerSolve1Id, $testAnswerSolve2Id, $testAnswerSolve4Id], $extractedAnswerIds);
    }

    #[Test]
    public function testExtractsEmptyArrayWhenNoChosenAnswers(): void
    {
        //Arrange
        $testAnswerSolve1 = $this->createMock(TestAnswerSolve::class);
        $testAnswerSolve1Id = Uuid::v7();
        $testAnswerSolve1->method('getAnswerId')->willReturn($testAnswerSolve1Id);
        $testAnswerSolve1->method('isChosen')->willReturn(false);

        $testAnswerSolve2 = $this->createMock(TestAnswerSolve::class);
        $testAnswerSolve2Id = Uuid::v7();
        $testAnswerSolve2->method('getAnswerId')->willReturn($testAnswerSolve2Id);
        $testAnswerSolve2->method('isChosen')->willReturn(false);

        $testQuestionSolve = new TestQuestionSolve();

        $testQuestionSolve->setTestAnswers([$testAnswerSolve1, $testAnswerSolve2]);
        
        //Act
        $extractedAnswerIds = $testQuestionSolve->extractChosenAnswerIds();

        //Assert
        $this->assertEquals([], $extractedAnswerIds);
    }

    #[Test]
    public function testExtractsEmptyArrayWhenNoAnswers(): void
    {
        //Arrange
        $testQuestionSolve = new TestQuestionSolve();

        //Act
        $extractedAnswerIds = $testQuestionSolve->extractChosenAnswerIds();

        //Assert
        $this->assertEquals([], $extractedAnswerIds);
    }
}