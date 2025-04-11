<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Answer\Model\AnswerModel;
use App\Application\Question\Command\AddQuestionToModule;
use App\Application\Question\Model\QuestionModel;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class AddQuestionToModuleTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('command.bus');
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testAddQuestionToModuleCommandPersistsQuestionCorrectly(): void
    {
        // Arrange
        $testModule = new Module();
        $testModule->setName('Test Module');
        $testModule->setLanguage('en');
        $testModule->setCategory('introduction');

        $this->entityManager->persist($testModule);
        $this->entityManager->flush();

        $questionModel = new QuestionModel();
        $questionModel->setContent('Test question');

        $questionModel->addAnswerModel((new AnswerModel())->setContent('Answer 1')->setCorrect(false));
        $questionModel->addAnswerModel((new AnswerModel())->setContent('Answer 2')->setCorrect(true));
        $questionModel->addAnswerModel((new AnswerModel())->setContent('Answer 3')->setCorrect(false));
        $questionModel->addAnswerModel((new AnswerModel())->setContent('Answer 4')->setCorrect(true));
        
        $command = new AddQuestionToModule($testModule->getId(), $questionModel);

        // Act
        $this->commandBus->dispatch($command);

        /**
         * @var Module $module
         */
        $module = $this->entityManager->getRepository(Module::class)->find($testModule->getId());

        // Assert
        $this->assertInstanceOf(Question::class, $module->getQuestions()[0]);
        $this->assertCount(1, $module->getQuestions());
        $this->assertNotNull($module->getQuestions()[0]->getId());
        $this->assertEquals('Test question', $module->getQuestions()[0]->getContent());
        
        $this->assertCount(4, $module->getQuestions()[0]->getAnswers());

        $this->assertInstanceOf(Answer::class, $module->getQuestions()[0]->getAnswers()[0]);
        $this->assertEquals('Answer 1', $module->getQuestions()[0]->getAnswers()[0]->getContent());
        $this->assertNotNull($module->getQuestions()[0]->getAnswers()[0]->getId());
        $this->assertFalse($module->getQuestions()[0]->getAnswers()[0]->isCorrect());
        $this->assertEquals(1, $module->getQuestions()[0]->getAnswers()[0]->getPosition());

        $this->assertInstanceOf(Answer::class, $module->getQuestions()[0]->getAnswers()[1]);
        $this->assertEquals('Answer 2', $module->getQuestions()[0]->getAnswers()[1]->getContent());
        $this->assertNotNull($module->getQuestions()[0]->getAnswers()[1]->getId());
        $this->assertTrue($module->getQuestions()[0]->getAnswers()[1]->isCorrect());
        $this->assertEquals(2, $module->getQuestions()[0]->getAnswers()[1]->getPosition());

        $this->assertInstanceOf(Answer::class, $module->getQuestions()[0]->getAnswers()[2]);
        $this->assertEquals('Answer 3', $module->getQuestions()[0]->getAnswers()[2]->getContent());
        $this->assertNotNull($module->getQuestions()[0]->getAnswers()[2]->getId());
        $this->assertFalse($module->getQuestions()[0]->getAnswers()[2]->isCorrect());
        $this->assertEquals(3, $module->getQuestions()[0]->getAnswers()[2]->getPosition());

        $this->assertInstanceOf(Answer::class, $module->getQuestions()[0]->getAnswers()[3]);
        $this->assertEquals('Answer 4', $module->getQuestions()[0]->getAnswers()[3]->getContent());
        $this->assertNotNull($module->getQuestions()[0]->getAnswers()[3]->getId());
        $this->assertTrue($module->getQuestions()[0]->getAnswers()[3]->isCorrect());
        $this->assertEquals(4, $module->getQuestions()[0]->getAnswers()[3]->getPosition());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testAddQuestionToModuleCommandThrowsExceptionWhenModuleNotExists(): void
    {
        $notExistingModuleId = Uuid::v4();

        $questionModel = new QuestionModel();

        $command = new AddQuestionToModule($notExistingModuleId, $questionModel);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Module {"id":"%s"}', $notExistingModuleId->toString()));

        $this->commandBus->dispatch($command);
    }
}