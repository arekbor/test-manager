<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Entity\Answer;
use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Answer\Model\AnswerModel;
use App\Application\Question\Model\QuestionModel;
use App\Application\Question\Command\UpdateQuestion\UpdateQuestion;
use App\Application\Shared\Bus\CommandBusInterface;

final class UpdateQuestionTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->getContainer()->get(CommandBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateQuestionCommandSuccessfullyUpdatesQuestion(): void
    {
        //Arrange
        $testModule = new Module();
        $testModule->setName('Test module');
        $testModule->setLanguage('pl');
        $testModule->setCategory('introduction');

        $testQuestion = new Question();
        $testQuestion->setContent('What is the capital of Poland?');
        $testQuestion->addAnswer((new Answer())->setContent('Warsaw')->setPosition(3)->setCorrect(false));
        $testQuestion->addAnswer((new Answer())->setContent('Krakow')->setPosition(5)->setCorrect(true));
        $testQuestion->addAnswer((new Answer())->setContent('Gdansk')->setPosition(10)->setCorrect(false));
        $testQuestion->addAnswer((new Answer())->setContent('Wroclaw')->setPosition(1)->setCorrect(false));

        $testQuestion->addModule($testModule);

        $this->entityManager->persist($testModule);
        $this->entityManager->persist($testQuestion);
        $this->entityManager->flush();

        $questionModel = new QuestionModel();
        $questionModel->setContent('Jaka jest stolica Polski?');
        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->first()->getId())
                ->setContent('Warszawa')
                ->setCorrect(true)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->get(1)->getId())
                ->setContent('Krakow')
                ->setCorrect(false)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->get(2)->getId())
                ->setContent('Gdansk')
                ->setCorrect(false)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->get(3)->getId())
                ->setContent('Wroclaw')
                ->setCorrect(false)
        );

        $command = new UpdateQuestion($testQuestion->getId(), $testModule->getId(), $questionModel);

        //Act
        $this->commandBus->handle($command);

        /**
         * @var Question $question
         */
        $question = $this->entityManager->getRepository(Question::class)->findOneBy(['id' => $testQuestion->getId()]);

        //Assert
        $this->assertEquals('Jaka jest stolica Polski?', $question->getContent());
        $this->assertEquals($testQuestion->getId(), $question->getId());
        $this->assertCount(4, $question->getAnswers());

        $this->assertEquals('Warszawa', $question->getAnswers()->first()->getContent());
        $this->assertEquals($testQuestion->getAnswers()->first()->getId(), $question->getAnswers()->first()->getId());
        $this->assertTrue($question->getAnswers()->first()->isCorrect());
        $this->assertEquals(1, $question->getAnswers()->first()->getPosition());

        $this->assertEquals('Krakow', $question->getAnswers()->get(1)->getContent());
        $this->assertEquals($testQuestion->getAnswers()->get(1)->getId(), $question->getAnswers()->get(1)->getId());
        $this->assertFalse($question->getAnswers()->get(1)->isCorrect());
        $this->assertEquals(2, $question->getAnswers()->get(1)->getPosition());

        $this->assertEquals('Gdansk', $question->getAnswers()->get(2)->getContent());
        $this->assertEquals($testQuestion->getAnswers()->get(2)->getId(), $question->getAnswers()->get(2)->getId());
        $this->assertFalse($question->getAnswers()->get(2)->isCorrect());
        $this->assertEquals(3, $question->getAnswers()->get(2)->getPosition());

        $this->assertEquals('Wroclaw', $question->getAnswers()->get(3)->getContent());
        $this->assertEquals($testQuestion->getAnswers()->get(3)->getId(), $question->getAnswers()->get(3)->getId());
        $this->assertFalse($question->getAnswers()->get(3)->isCorrect());
        $this->assertEquals(4, $question->getAnswers()->get(3)->getPosition());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateQuestionCommandUpdatesExistingAnswersAndAddsNewAnswers(): void
    {
        //Arrange
        $testModule = new Module();
        $testModule->setName('Test module');
        $testModule->setLanguage('pl');
        $testModule->setCategory('introduction');

        $testQuestion = new Question();
        $testQuestion->setContent('What is the capital of Poland?');
        $testQuestion->addAnswer((new Answer())->setContent('Warsaw')->setPosition(3)->setCorrect(false));
        $testQuestion->addAnswer((new Answer())->setContent('Krakow')->setPosition(5)->setCorrect(true));
        $testQuestion->addAnswer((new Answer())->setContent('Gdansk')->setPosition(10)->setCorrect(false));
        $testQuestion->addAnswer((new Answer())->setContent('Wroclaw')->setPosition(1)->setCorrect(false));

        $testQuestion->addModule($testModule);

        $this->entityManager->persist($testModule);
        $this->entityManager->persist($testQuestion);
        $this->entityManager->flush();

        $questionModel = new QuestionModel();
        $questionModel->setContent('Jaka jest stolica Polski?');
        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->first()->getId())
                ->setContent('Warszawa')
                ->setCorrect(true)
        );
        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->get(1)->getId())
                ->setContent('Krakow')
                ->setCorrect(false)
        );
        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->get(2)->getId())
                ->setContent('Gdansk')
                ->setCorrect(false)
        );
        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->get(3)->getId())
                ->setContent('Wroclaw')
                ->setCorrect(false)
        );

        // Adding a new answer model
        $questionModel->addAnswerModel((new AnswerModel())->setContent('Poznan')->setCorrect(false));
        $questionModel->addAnswerModel((new AnswerModel())->setContent('Lodz')->setCorrect(false));

        $command = new UpdateQuestion($testQuestion->getId(), $testModule->getId(), $questionModel);

        //Act
        $this->commandBus->handle($command);

        /**
         * @var Question $question
         */
        $question = $this->entityManager->getRepository(Question::class)->findOneBy(['id' => $testQuestion->getId()]);

        //Assert
        $this->assertEquals('Jaka jest stolica Polski?', $question->getContent());
        $this->assertEquals($question->getId(), $testQuestion->getId());
        $this->assertCount(6, $question->getAnswers());

        $this->assertEquals('Warszawa', $question->getAnswers()->first()->getContent());
        $this->assertEquals($question->getAnswers()->first()->getId(), $testQuestion->getAnswers()->first()->getId());
        $this->assertTrue($question->getAnswers()->first()->isCorrect());
        $this->assertEquals(1, $question->getAnswers()->first()->getPosition());

        $this->assertEquals('Krakow', $question->getAnswers()->get(1)->getContent());
        $this->assertEquals($question->getAnswers()->get(1)->getId(), $testQuestion->getAnswers()->get(1)->getId());
        $this->assertFalse($question->getAnswers()->get(1)->isCorrect());
        $this->assertEquals(2, $question->getAnswers()->get(1)->getPosition());

        $this->assertEquals('Gdansk', $question->getAnswers()->get(2)->getContent());
        $this->assertEquals($question->getAnswers()->get(2)->getId(), $testQuestion->getAnswers()->get(2)->getId());
        $this->assertFalse($question->getAnswers()->get(2)->isCorrect());
        $this->assertEquals(3, $question->getAnswers()->get(2)->getPosition());

        $this->assertEquals('Wroclaw', $question->getAnswers()->get(3)->getContent());
        $this->assertEquals($question->getAnswers()->get(3)->getId(), $testQuestion->getAnswers()->get(3)->getId());
        $this->assertFalse($question->getAnswers()->get(3)->isCorrect());
        $this->assertEquals(4, $question->getAnswers()->get(3)->getPosition());

        $this->assertEquals('Poznan', $question->getAnswers()->get(4)->getContent());
        $this->assertNotNull($question->getAnswers()->get(4)->getId());
        $this->assertFalse($question->getAnswers()->get(4)->isCorrect());
        $this->assertEquals(5, $question->getAnswers()->get(4)->getPosition());

        $this->assertEquals('Lodz', $question->getAnswers()->get(5)->getContent());
        $this->assertNotNull($question->getAnswers()->get(5)->getId());
        $this->assertFalse($question->getAnswers()->get(5)->isCorrect());
        $this->assertEquals(6, $question->getAnswers()->get(5)->getPosition());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateQuestionCommandRemovesDeletedAnswersAndAddsNewAnswers(): void
    {
        //Arrange
        $testModule = new Module();
        $testModule->setName('Test module');
        $testModule->setLanguage('en');
        $testModule->setCategory('introduction');

        $testQuestion = new Question();
        $testQuestion->setContent('Most popular programming language?');
        $testQuestion->addAnswer((new Answer())->setContent('Java')->setPosition(3)->setCorrect(false));
        $testQuestion->addAnswer((new Answer())->setContent('C#')->setPosition(5)->setCorrect(true));
        $testQuestion->addAnswer((new Answer())->setContent('Python')->setPosition(10)->setCorrect(false));
        $testQuestion->addAnswer((new Answer())->setContent('JavaScript')->setPosition(1)->setCorrect(false));

        $testQuestion->addModule($testModule);

        $this->entityManager->persist($testModule);
        $this->entityManager->persist($testQuestion);
        $this->entityManager->flush();

        $questionModel = new QuestionModel();
        $questionModel->setContent('Jaki jest najpopularniejszy język programowania?');

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->first()->getId())
                ->setContent('Java')
                ->setCorrect(true)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setAnswerId($testQuestion->getAnswers()->get(1)->getId())
                ->setContent('C#')
                ->setCorrect(false)
        );

        // Adding a new answer model
        $questionModel->addAnswerModel((new AnswerModel())->setContent('PHP')->setCorrect(false));
        $questionModel->addAnswerModel((new AnswerModel())->setContent('Ruby')->setCorrect(false));
        $questionModel->addAnswerModel((new AnswerModel())->setContent('Go')->setCorrect(false));

        $command = new UpdateQuestion($testQuestion->getId(), $testModule->getId(), $questionModel);

        //Act
        $this->commandBus->handle($command);

        $this->entityManager->clear();

        /**
         * @var Question $question
         */
        $question = $this->entityManager->getRepository(Question::class)->findOneBy(['id' => $testQuestion->getId()]);

        //Assert
        $this->assertEquals('Jaki jest najpopularniejszy język programowania?', $question->getContent());
        $this->assertEquals($question->getId(), $testQuestion->getId());
        $this->assertCount(5, $question->getAnswers());

        $this->assertEquals('Java', $question->getAnswers()->first()->getContent());
        $this->assertEquals($question->getAnswers()->first()->getId(), $testQuestion->getAnswers()->first()->getId());
        $this->assertTrue($question->getAnswers()->first()->isCorrect());
        $this->assertEquals(1, $question->getAnswers()->first()->getPosition());

        $this->assertEquals('C#', $question->getAnswers()->get(1)->getContent());
        $this->assertEquals($question->getAnswers()->get(1)->getId(), $testQuestion->getAnswers()->get(1)->getId());
        $this->assertFalse($question->getAnswers()->get(1)->isCorrect());
        $this->assertEquals(2, $question->getAnswers()->get(1)->getPosition());

        $this->assertEquals('PHP', $question->getAnswers()->get(2)->getContent());
        $this->assertNotNull($question->getAnswers()->get(2)->getId());
        $this->assertFalse($question->getAnswers()->get(2)->isCorrect());
        $this->assertEquals(3, $question->getAnswers()->get(2)->getPosition());

        $this->assertEquals('Ruby', $question->getAnswers()->get(3)->getContent());
        $this->assertNotNull($question->getAnswers()->get(3)->getId());
        $this->assertFalse($question->getAnswers()->get(3)->isCorrect());
        $this->assertEquals(4, $question->getAnswers()->get(3)->getPosition());

        $this->assertEquals('Go', $question->getAnswers()->get(4)->getContent());
        $this->assertNotNull($question->getAnswers()->get(4)->getId());
        $this->assertFalse($question->getAnswers()->get(4)->isCorrect());
        $this->assertEquals(5, $question->getAnswers()->get(4)->getPosition());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateQuestionByReplacingAllExistingAnswersWithNewAnswers(): void
    {
        //Arrange
        $testModule = new Module();
        $testModule->setName('Test module');
        $testModule->setLanguage('pl');
        $testModule->setCategory('introduction');

        $testQuestion = new Question();
        $testQuestion->setContent('How many days are in a week?');
        $testQuestion->addAnswer((new Answer())->setContent('5')->setPosition(3)->setCorrect(false));
        $testQuestion->addAnswer((new Answer())->setContent('6')->setPosition(5)->setCorrect(true));
        $testQuestion->addAnswer((new Answer())->setContent('7')->setPosition(10)->setCorrect(false));
        $testQuestion->addAnswer((new Answer())->setContent('8')->setPosition(1)->setCorrect(false));

        $testModule->addQuestion($testQuestion);

        $this->entityManager->persist($testModule);
        $this->entityManager->persist($testQuestion);
        $this->entityManager->flush();

        $this->entityManager->clear();

        $questionModel = new QuestionModel();
        $questionModel->setContent('Ile dni ma tydzień?');

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setContent('Pięć')
                ->setCorrect(false)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setContent('Sześć')
                ->setCorrect(true)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setContent('Siedem')
                ->setCorrect(false)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setContent('Osiem')
                ->setCorrect(false)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setContent('Dziesięć')
                ->setCorrect(false)
        );

        $questionModel->addAnswerModel(
            (new AnswerModel())
                ->setContent('Dwanaście')
                ->setCorrect(false)
        );

        $command = new UpdateQuestion($testQuestion->getId(), $testModule->getId(), $questionModel);

        //Act
        $this->commandBus->handle($command);

        $this->entityManager->clear();

        /**
         * @var Question $question
         */
        $question = $this->entityManager->getRepository(Question::class)->findOneBy(['id' => $testQuestion->getId()]);

        //Assert
        $this->assertEquals('Ile dni ma tydzień?', $question->getContent());
        $this->assertEquals($question->getId(), $testQuestion->getId());
        $this->assertCount(6, $question->getAnswers());

        $this->assertEquals('Pięć', $question->getAnswers()->first()->getContent());
        $this->assertNotEquals($question->getAnswers()->first()->getId(), $testQuestion->getAnswers()->first()->getId());
        $this->assertFalse($question->getAnswers()->first()->isCorrect());
        $this->assertEquals(1, $question->getAnswers()->first()->getPosition());

        $this->assertEquals('Sześć', $question->getAnswers()->get(1)->getContent());
        $this->assertNotEquals($question->getAnswers()->get(1)->getId(), $testQuestion->getAnswers()->get(1)->getId());
        $this->assertTrue($question->getAnswers()->get(1)->isCorrect());
        $this->assertEquals(2, $question->getAnswers()->get(1)->getPosition());

        $this->assertEquals('Siedem', $question->getAnswers()->get(2)->getContent());
        $this->assertNotEquals($question->getAnswers()->get(2)->getId(), $testQuestion->getAnswers()->get(2)->getId());
        $this->assertFalse($question->getAnswers()->get(2)->isCorrect());
        $this->assertEquals(3, $question->getAnswers()->get(2)->getPosition());

        $this->assertEquals('Osiem', $question->getAnswers()->get(3)->getContent());
        $this->assertNotEquals($question->getAnswers()->get(3)->getId(), $testQuestion->getAnswers()->get(3)->getId());
        $this->assertFalse($question->getAnswers()->get(3)->isCorrect());
        $this->assertEquals(4, $question->getAnswers()->get(3)->getPosition());

        $this->assertEquals('Dziesięć', $question->getAnswers()->get(4)->getContent());
        $this->assertNotNull($question->getAnswers()->get(4)->getId());
        $this->assertFalse($question->getAnswers()->get(4)->isCorrect());
        $this->assertEquals(5, $question->getAnswers()->get(4)->getPosition());

        $this->assertEquals('Dwanaście', $question->getAnswers()->get(5)->getContent());
        $this->assertNotNull($question->getAnswers()->get(5)->getId());
        $this->assertFalse($question->getAnswers()->get(5)->isCorrect());
        $this->assertEquals(6, $question->getAnswers()->get(5)->getPosition());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateQuestionCommandThrowsExceptionWhenModuleAndQuestionNotExists(): void
    {
        $notExistingModuleId = Uuid::v4();
        $notExistingQuestionId = Uuid::v4();

        $command = new UpdateQuestion($notExistingQuestionId, $notExistingModuleId, new QuestionModel());

        $this->expectExceptionMessage(sprintf(
            'App\Domain\Entity\Question {"questionId":"%s","moduleId":"%s"}',
            $notExistingQuestionId->toString(),
            $notExistingModuleId->toString()
        ));

        $this->commandBus->handle($command);
    }
}
