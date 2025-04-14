<?php 

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\Domain\Entity\Module;
use App\Domain\Entity\Test;
use App\Domain\Entity\Video;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class TestTest extends TestCase
{
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