<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Shared\QueryBusInterface;
use App\Application\Test\Model\DataForTestSolve;
use App\Application\Test\Model\TestAnswerSolve;
use App\Application\Test\Model\TestQuestionSolve;
use App\Application\Test\Model\TestSolve;
use App\Application\Test\Query\GetDataForTestSolve;
use App\Application\Video\Model\TestVideo;
use App\Domain\Entity\Answer;
use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use App\Domain\Entity\SecurityUser;
use App\Domain\Entity\Test as EntityTest;
use App\Domain\Entity\Video;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\Collection;

final class GetDataForTestSolveTest extends DatabaseTestCase
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
    public function testGetDataForTestSolveQueryReturnsDataForTestSolveCorrectly(): void
    {
        //Arrange
        $testQuestion = new Question();
        $testQuestion->setContent('Test question');

        $testQuestion->addAnswer((new Answer())->setContent('Answer 1')->setCorrect(false)->setPosition(1));
        $testQuestion->addAnswer((new Answer())->setContent('Answer 2')->setCorrect(true)->setPosition(2));
        $testQuestion->addAnswer((new Answer())->setContent('Answer 3')->setCorrect(false)->setPosition(3));
        $testQuestion->addAnswer((new Answer())->setContent('Answer 4')->setCorrect(true)->setPosition(4));

        $testVideo = new Video();

        $testModule = new Module();
        $testModule->setName('Test module');
        $testModule->setLanguage('pl');
        $testModule->setCategory('introduction');
        $testModule->addQuestion($testQuestion);
        $testModule->addVideo($testVideo);

        $testSecurityUser = new SecurityUser();
        $testSecurityUser->setEmail('test@gmail.com');
        $testSecurityUser->setPassword('secret');

        $entityTest = new EntityTest();
        $entityTest->setExpiration((new \DateTime())->modify('+1 week'));
        $entityTest->setModule($testModule);
        $entityTest->setCreator($testSecurityUser);

        $this->entityManager->persist($testVideo);
        $this->entityManager->persist($testQuestion);
        $this->entityManager->persist($testModule);
        $this->entityManager->persist($testSecurityUser);
        $this->entityManager->persist($entityTest);

        $this->entityManager->flush();
    
        $query = new GetDataForTestSolve($entityTest->getId());

        //Act
        /**
         * @var DataForTestSolve $dataForTestSolve
         */
        $dataForTestSolve = $this->queryBus->query($query);
        
        //Assert
        $this->assertInstanceOf(DataForTestSolve::class, $dataForTestSolve);

        $this->assertEquals('introduction', $dataForTestSolve->getTestCategory());
        $this->assertEquals($entityTest->getId(), $dataForTestSolve->getTestId());
        
        $this->assertInstanceOf(TestSolve::class, $dataForTestSolve->getTestSolve());

        $this->assertFalse($dataForTestSolve->getTestSolve()->isConsent());

        $testQuestionSolves = $dataForTestSolve->getTestSolve()->getTestQuestionSolves();

        $this->assertCount(1, $testQuestionSolves);

        $this->assertInstanceOf(TestQuestionSolve::class, $testQuestionSolves->first());
        $this->assertEquals($testQuestion->getId(), $testQuestionSolves->first()->getQuestionId());
        $this->assertEquals($testQuestion->getContent(), $testQuestionSolves->first()->getContent());

        /**
         * @var Collection<int, TestAnswerSolve>
         */
        $testAnswerSolves = $dataForTestSolve->getTestSolve()->getTestQuestionSolves()->first()->getTestAnswerSolves();
    
        $this->assertCount(4, $testAnswerSolves);

        $this->assertInstanceOf(TestAnswerSolve::class, $testAnswerSolves->first());
        $this->assertEquals($testQuestion->getAnswers()->first()->getId(), $testAnswerSolves->first()->getAnswerId());
        $this->assertEquals('Answer 1', $testAnswerSolves->first()->getContent());
        $this->assertFalse($testAnswerSolves->first()->isChosen());

        $this->assertInstanceOf(TestAnswerSolve::class, $testAnswerSolves->get(1));
        $this->assertEquals($testQuestion->getAnswers()->get(1)->getId(), $testAnswerSolves->get(1)->getAnswerId());
        $this->assertEquals('Answer 2', $testAnswerSolves->get(1)->getContent());
        $this->assertFalse($testAnswerSolves->first()->isChosen());

        $this->assertInstanceOf(TestAnswerSolve::class, $testAnswerSolves->get(2));
        $this->assertEquals($testQuestion->getAnswers()->get(2)->getId(), $testAnswerSolves->get(2)->getAnswerId());
        $this->assertEquals('Answer 3', $testAnswerSolves->get(2)->getContent());
        $this->assertFalse($testAnswerSolves->first()->isChosen());

        $this->assertInstanceOf(TestAnswerSolve::class, $testAnswerSolves->get(3));
        $this->assertEquals($testQuestion->getAnswers()->get(3)->getId(), $testAnswerSolves->get(3)->getAnswerId());
        $this->assertEquals('Answer 4', $testAnswerSolves->get(3)->getContent());
        $this->assertFalse($testAnswerSolves->first()->isChosen());

        $this->assertCount(1, $dataForTestSolve->getTestVideos());

        $this->assertInstanceOf(TestVideo::class, $dataForTestSolve->getTestVideos()[0]);
        $this->assertEquals($testVideo->getId(), $dataForTestSolve->getTestVideos()[0]->getVideoId());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetDataForTestSolveQueryThrowsExceptionWhenTestNotFound(): void
    {
        $notExistingTestId = Uuid::v4();

        $query = new GetDataForTestSolve($notExistingTestId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Test {"id":"%s"}', $notExistingTestId->toString()));

        $this->queryBus->query($query);
    }
}