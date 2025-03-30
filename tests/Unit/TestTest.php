<?php 

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\Domain\Entity\Answer;
use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use App\Domain\Entity\Test;
use App\Domain\Entity\Video;
use App\Application\Test\Model\TestSolve;
use App\Application\Test\Service\TestSolveFactory;
use App\Tests\EntityHelper;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class TestTest extends TestCase
{
    #[Test]
    public function testConvertsTestToTestSolveCorrectly(): void
    {
        //Arrange
        $answer1 = new Answer();
        EntityHelper::setId($answer1Id = Uuid::v7(), Answer::class, $answer1);
        $answer1->setContent($answer1Content = 'Answer 1');

        $answer2 = new Answer();
        EntityHelper::setId($answer2Id = Uuid::v7(), Answer::class, $answer2);
        $answer2->setContent($answer2Content = 'Answer 2');

        $question = new Question();
        EntityHelper::setId($questionId = Uuid::v7(), Question::class, $question);
        $question->setContent($questionContent = 'Sample Question?');
        $question->addAnswer($answer1);
        $question->addAnswer($answer2);

        $module = new Module();
        $module->addQuestion($question);

        $test = new Test();
        $test->setModule($module);

        //Act
        $testSolve = TestSolveFactory::createFromModule($test->getModule());

        $testSolveQuestions = $testSolve->getTestQuestions();
        $testSolveAnswers = $testSolveQuestions[0]->getTestAnswers();

        //Assert
        $this->assertInstanceOf(TestSolve::class, $testSolve);

        $this->assertEquals(1, count($testSolveQuestions));
        $this->assertEquals($questionId, $testSolveQuestions[0]->getQuestionId());
        $this->assertEquals($questionContent, $testSolveQuestions[0]->getContent());

        $this->assertEquals(2, count($testSolveAnswers));

        $this->assertEquals($answer1Id, $testSolveAnswers[0]->getAnswerId());
        $this->assertEquals($answer1Content, $testSolveAnswers[0]->getContent());

        $this->assertEquals($answer2Id, $testSolveAnswers[1]->getAnswerId());
        $this->assertEquals($answer2Content, $testSolveAnswers[1]->getContent());
    }

    #[Test]
    #[DataProvider('featureModifierProvider')]
    public function testIsValidReturnsTrueWhenExpirationIsInTheFuture(string $modifier): void
    {
        //Arrange
        $test = new Test();
        $test->setExpiration((new \DateTime())->modify($modifier));
        $test->setSubmission(null);

        //Act
        $isValid = $test->isValid();

        //Assert
        $this->assertTrue($isValid, 'Should return true when expiration is in the future.');
    }

    #[Test]
    public function testIsNotValidWhenSubmissionIsNotNull(): void
    {
        //Arrange
        $test = new Test();
        $test->setExpiration((new \DateTime())->modify('+5 days'));
        $test->setSubmission(new \DateTime());

        //Act
        $isValid = $test->isValid();

        //Assert
        $this->assertFalse($isValid, 'Should return false when submission is not null.');
    }

    #[Test]
    #[DataProvider('pastModifierProvider')]
    public function testIsNotValidWhenExpirationIsInThePast(string $modifier): void
    {
        //Arrange
        $test = new Test();
        $test->setExpiration((new \DateTime())->modify($modifier));
        $test->setSubmission(null);

        //Act
        $isValid = $test->isValid();

        //Assert
        $this->assertFalse($isValid, 'Should return false when expiration is in the past.');
    }

    #[Test]
    public function testIsNotValidWhenExpirationIsNull(): void
    {
        //Arrange
        $test = new Test();
        $test->setExpiration(null);

        //Act
        $isValid = $test->isValid();

        //Assert
        $this->assertFalse($isValid, 'Should return false when expiration is null.');
    }

    #[Test]
    public function testReturnsTrueWhenVideoIdMatchesBetweenTestAndModule(): void
    {
        //Arrange
        $uuid = Uuid::v7();

        $videoMock = $this->createVideoMock($uuid);
        $videoInModuleMock = $this->createVideoMock($uuid);

        $test = $this->createTestWithModuleAndVideos([$videoInModuleMock]);
        
        //Act
        $belongs = $test->videoBelongsToTest($videoMock);

        //Assert
        $this->assertTrue($belongs, 'Should return true when the video ID match any video in the test\'s module.');
    }

    #[Test]
    public function testReturnsFalseWhenVideoIdDoesNotMatchInTestModule(): void
    {
        //Arrange
        $videoMock = $this->createVideoMock(Uuid::v7());
        $videoInModuleMock = $this->createVideoMock(Uuid::v7());

        $test = $this->createTestWithModuleAndVideos([$videoInModuleMock]);

        //Act
        $belongs = $test->videoBelongsToTest($videoMock);

        //Assert
        $this->assertFalse($belongs, 'Should return false when the video ID does not match any video in the test\'s module.');
    }

    #[Test]
    public function testReturnsFalseWhenNoVideosInTestModule(): void
    {
        //Arrange
        $videoMock = $this->createVideoMock(Uuid::v7());
        $test = $this->createTestWithModuleAndVideos([]);

        //Act
        $belongs = $test->videoBelongsToTest($videoMock);

        //Assert
        $this->assertFalse($belongs, 'Should return false when there are no videos in the test\'s module.');
    }

    public static function featureModifierProvider(): array
    {
        return [
            ['+1 seconds'],
            ['+1 minutes'],
            ['+3 hours'],
            ['+5 days'],
            ['+12 months']
        ];
    }

    public static function pastModifierProvider(): array
    {
        return [
            ['-12 seconds'],
            ['-30 minutes'],
            ['-1 hours'],
            ['-3 days'],
            ['-50 months']
        ];
    }

    private function createTestWithModuleAndVideos(array $videos): Test
    {
        $videosCollection = new ArrayCollection($videos);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->method('getVideos')->willReturn($videosCollection);

        $testMock = $this->getMockBuilder(Test::class)
            ->onlyMethods(['getModule'])
            ->getMock();
        $testMock->method('getModule')->willReturn($moduleMock);

        return $testMock;
    }

    private function createVideoMock(Uuid $videoId): Video
    {
        $videoMock = $this->createMock(Video::class);
        $videoMock->method('getId')->willReturn($videoId);

        return $videoMock;
    }
}