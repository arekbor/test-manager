<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Entity\Answer;
use App\Domain\Entity\Question;
use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Answer\Model\AnswerModel;
use App\Application\Question\Model\QuestionModel;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\Question\Query\GetQuestionModel\GetQuestionModel;

final class GetQuestionModelTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly QueryBusInterface $queryBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryBus = self::getContainer()->get(QueryBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetQuestionModelQueryReturnsQuestionSuccessfully(): void
    {
        // Arrange
        $question = new Question();
        $question->setContent('Test to find by query');

        $question->addAnswer((new Answer())->setContent('Answer 1')->setPosition(1)->setCorrect(true));
        $question->addAnswer((new Answer())->setContent('Answer 2')->setPosition(2)->setCorrect(true));
        $question->addAnswer((new Answer())->setContent('Answer 3')->setPosition(3)->setCorrect(false));
        $question->addAnswer((new Answer())->setContent('Answer 4')->setPosition(4)->setCorrect(true));

        $this->entityManager->persist($question);
        $this->entityManager->flush();

        $query = new GetQuestionModel($question->getId());

        // Act

        /**
         * @var QuestionModel $questioModel
         */
        $questioModel = $this->queryBus->ask($query);

        // Assert
        $this->assertInstanceOf(QuestionModel::class, $questioModel);
        $this->assertEquals('Test to find by query', $questioModel->getContent());
        $this->assertCount(4, $questioModel->getAnswerModels());

        $this->assertInstanceOf(AnswerModel::class, $questioModel->getAnswerModels()[0]);
        $this->assertEquals('Answer 1', $questioModel->getAnswerModels()[0]->getContent());
        $this->assertTrue($questioModel->getAnswerModels()[0]->isCorrect());
        $this->assertEquals($question->getAnswers()[0]->getId(), $questioModel->getAnswerModels()[0]->getAnswerId());

        $this->assertInstanceOf(AnswerModel::class, $questioModel->getAnswerModels()[1]);
        $this->assertEquals('Answer 2', $questioModel->getAnswerModels()[1]->getContent());
        $this->assertTrue($questioModel->getAnswerModels()[1]->isCorrect());
        $this->assertEquals($question->getAnswers()[1]->getId(), $questioModel->getAnswerModels()[1]->getAnswerId());

        $this->assertInstanceOf(AnswerModel::class, $questioModel->getAnswerModels()[2]);
        $this->assertEquals('Answer 3', $questioModel->getAnswerModels()[2]->getContent());
        $this->assertFalse($questioModel->getAnswerModels()[2]->isCorrect());
        $this->assertEquals($question->getAnswers()[2]->getId(), $questioModel->getAnswerModels()[2]->getAnswerId());

        $this->assertInstanceOf(AnswerModel::class, $questioModel->getAnswerModels()[3]);
        $this->assertEquals('Answer 4', $questioModel->getAnswerModels()[3]->getContent());
        $this->assertTrue($questioModel->getAnswerModels()[3]->isCorrect());
        $this->assertEquals($question->getAnswers()[3]->getId(), $questioModel->getAnswerModels()[3]->getAnswerId());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetQuestionModelQueryThrowsExceptionWhenQuestionNotFound(): void
    {
        $notExistingModuleId = Uuid::v4();

        $query = new GetQuestionModel($notExistingModuleId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Question {"id":"%s"}', $notExistingModuleId->toString()));

        $this->queryBus->ask($query);
    }
}
