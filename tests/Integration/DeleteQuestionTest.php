<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Entity\Question;
use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Question\Command\DeleteQuestion\DeleteQuestion;
use App\Application\Shared\Bus\CommandBusInterface;

final class DeleteQuestionTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testDeleteQuestionCommandDeletesQuestionSuccessfully(): void
    {
        //Arrange
        $testQuestion = new Question();
        $testQuestion->setContent('Test');

        $this->entityManager->persist($testQuestion);
        $this->entityManager->flush();

        $questionId = $testQuestion->getId();

        $command = new DeleteQuestion($questionId);

        //Act
        $this->commandBus->handle($command);

        /**
         * @var Question|null $question
         */
        $question = $this->entityManager->find(Question::class, $questionId);

        //Assert
        $this->assertNull($question);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testDeleteQuestionCommandThrowsExceptionWhenQuestionNotFound(): void
    {
        $notExistingQuestionId = Uuid::v4();

        $command = new DeleteQuestion($notExistingQuestionId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Question {"id":"%s"}', $notExistingQuestionId->toString()));

        $this->commandBus->handle($command);
    }
}
