<?php 

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ModuleTest extends TestCase
{
    public function testFindQuestionByIdReturnsCorrectQuestion(): void
    {
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

        $foundQuestion = $module->findQuestionById($question2Id);

        $this->assertSame($question2, $foundQuestion);
    }

    public function testFindQuestionByIdReturnsFirstQuestionWhenDuplicateIdsExist(): void
    {
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

        $foundQuestion = $module->findQuestionById($duplicateId);

        $this->assertSame($question1, $foundQuestion);
    }

    public function testFindQuestionByIdReturnsNullWhenIdNotFound(): void
    {
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

        $foundQuestion = $module->findQuestionById($nonExistentId);

        $this->assertNull($foundQuestion);
    }
}