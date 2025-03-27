<?php 

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class ModuleTest extends TestCase
{
    #[Test]
    public function testFindQuestionByIdReturnsCorrectQuestion(): void
    {
        //Arrange
        $question1 = $this->createMock(Question::class);
        $question1Id = Uuid::v7();
        $question1->method('getId')->willReturn($question1Id);

        $question2 = $this->createMock(Question::class);
        $question2Id = Uuid::v7();
        $question2->method('getId')->willReturn($question2Id);

        $question3 = $this->createMock(Question::class);
        $question3Id = Uuid::v7();
        $question3->method('getId')->willReturn($question3Id);

        $module = new Module();
        $module
            ->addQuestion($question1)
            ->addQuestion($question2)
            ->addQuestion($question3)
        ;

        //Act
        $foundQuestion = $module->findQuestionById($question2Id);

        //Assert
        $this->assertSame($question2, $foundQuestion);
    }

    #[Test]
    public function testFindQuestionByIdReturnsFirstQuestionWhenDuplicateIdsExist(): void
    {
        //Arrange
        $duplicateId = Uuid::v7();

        $question1 = $this->createMock(Question::class);
        $question1->method('getId')->willReturn($duplicateId);

        $question2 = $this->createMock(Question::class);
        $question2->method('getId')->willReturn($duplicateId);

        $module = new Module();
        $module
            ->addQuestion($question1)
            ->addQuestion($question2)
        ;

        //Act
        $foundQuestion = $module->findQuestionById($duplicateId);

        //Assert
        $this->assertSame($question1, $foundQuestion);
    }

    #[Test]
    public function testFindQuestionByIdReturnsNullWhenIdNotFound(): void
    {
        //Assert
        $question1 = $this->createMock(Question::class);
        $question1Id = Uuid::v7();
        $question1->method('getId')->willReturn($question1Id);

        $question2 = $this->createMock(Question::class);
        $question2Id = Uuid::v7();
        $question2->method('getId')->willReturn($question2Id);

        $question3 = $this->createMock(Question::class);
        $question3Id = Uuid::v7();
        $question3->method('getId')->willReturn($question3Id);

        $module = new Module();
        $module
            ->addQuestion($question1)
            ->addQuestion($question2)
            ->addQuestion($question3);

        $nonExistentId = Uuid::v7();

        //Act
        $foundQuestion = $module->findQuestionById($nonExistentId);

        //Assert
        $this->assertNull($foundQuestion);
    }
}