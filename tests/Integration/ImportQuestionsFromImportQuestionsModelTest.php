<?php

declare(strict_types=1);

use App\Application\Answer\Model\AnswerModel;
use App\Application\Question\Command\ImportQuestionsFromImportQuestionsModel;
use App\Application\Question\Model\ImportQuestionsModel;
use App\Application\Question\Model\QuestionModel;
use App\Domain\Entity\Module;
use App\Tests\DatabaseTestCase;
use App\Tests\Integration\IntegrationTestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;

final class ImportQuestionsFromImportQuestionsModelTest extends DatabaseTestCase
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
    public function testImportQuestionsFromImportQuestionsModelAddsNewQuestionsCorrectly(): void
    {
        //Arrange
        $testModule = new Module();
        $testModule->setName('Test module');
        $testModule->setLanguage('pl');
        $testModule->setCategory('introduction');

        $this->entityManager->persist($testModule);
        $this->entityManager->flush();

        $importQuestionsModel = new ImportQuestionsModel();

        $questionModel1 = new QuestionModel();
        $questionModel1->setContent('Question model 1 content');
        $questionModel1->addAnswerModel((new AnswerModel())->setContent('Answer 1 of question model 1')->setCorrect(false));
        $questionModel1->addAnswerModel((new AnswerModel())->setContent('Answer 2 of question model 1')->setCorrect(false));
        $questionModel1->addAnswerModel((new AnswerModel())->setContent('Answer 3 of question model 1')->setCorrect(false));
        $questionModel1->addAnswerModel((new AnswerModel())->setContent('Answer 4 of question model 1')->setCorrect(true));

        $importQuestionsModel->addQuestionModel($questionModel1);

        $questionModel2 = new QuestionModel();
        $questionModel2->setContent('Question model 2 content');
        $questionModel2->addAnswerModel((new AnswerModel())->setContent('Answer 1 of question model 2')->setCorrect(false));
        $questionModel2->addAnswerModel((new AnswerModel())->setContent('Answer 2 of question model 2')->setCorrect(false));
        $questionModel2->addAnswerModel((new AnswerModel())->setContent('Answer 3 of question model 2')->setCorrect(false));
        $questionModel2->addAnswerModel((new AnswerModel())->setContent('Answer 4 of question model 2')->setCorrect(true));

        $importQuestionsModel->addQuestionModel($questionModel2);

        $moduleId = $testModule->getId();
        //Act
        $this->commandBus->dispatch(new ImportQuestionsFromImportQuestionsModel($moduleId, $importQuestionsModel));

        /**
         * @var Module $module
         */
        $module = $this->entityManager->getRepository(Module::class)->find($moduleId);

        //Assert
        $questions = $module->getQuestions();

        $this->assertCount(2, $questions);
    }
}
