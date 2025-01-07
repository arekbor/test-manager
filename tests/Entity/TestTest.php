<?php 

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Module;
use App\Entity\Test;
use App\Entity\Video;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TestTest extends TestCase
{
    public function testIsValidWhenExpirationInFeature(): void
    {
        $test = new Test();
        $test->setExpiration((new DateTime())->modify('+5 days'));
        $test->setSubmission(null);

        $this->assertTrue($test->isValid(), 'Should return true when expiration is in the future.');
    }

    public function testIsNotValidWhenSubmissionIsNotNull(): void
    {
        $test = new Test();
        $test->setExpiration((new DateTime())->modify('+5 days'));
        $test->setExpiration(new DateTime());

        $this->assertFalse($test->isValid(), 'Should return false when submission is not null');
    }

    public function testIsNotValidWhenExpirationInPast(): void
    {
        $test = new Test();
        $test->setExpiration((new DateTime())->modify('-12 hours'));
        $test->setSubmission(null);

        $this->assertFalse($test->isValid(), 'Should return false when expiration is in the past');
    }

    public function testIsNotValidWhenExpirationIsNull(): void
    {
        $test = new Test();
        $test->setExpiration(null);

        $this->assertFalse($test->isValid(), 'Should return false when expiration is null');
    }

    public function testVideoBelongsToTest(): void
    {
        $videoMock = $this->createMock(Video::class);
        $videoMock->method('getId')->willReturn(1);

        $videoInModuleMock = $this->createMock(Video::class);
        $videoInModuleMock->method('getId')->willReturn(1);

        $videosCollection = new ArrayCollection([$videoInModuleMock]);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->method('getVideos')->willReturn($videosCollection);

        $test = $this->getMockBuilder(Test::class)
            ->onlyMethods(['getModule'])
            ->getMock();
        $test->method('getModule')->willReturn($moduleMock);

        $this->assertTrue($test->videoBelongsToTest($videoMock));
    }

    public function testVideoDoesNotBelongToTest(): void
    {
        $videoMock = $this->createMock(Video::class);
        $videoMock->method('getId')->willReturn(2);

        $videoInModuleMock = $this->createMock(Video::class);
        $videoInModuleMock->method('getId')->willReturn(1);

        $videosCollection = new ArrayCollection([$videoInModuleMock]);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->method('getVideos')->willReturn($videosCollection);

        $test = $this->getMockBuilder(Test::class)
            ->onlyMethods(['getModule'])
            ->getMock();
        $test->method('getModule')->willReturn($moduleMock);

        $this->assertFalse($test->videoBelongsToTest($videoMock));
    }

    public function testVideoBelongsToTestWithEmptyVideoCollection(): void
    {
        $videoMock = $this->createMock(Video::class);
        $videoMock->method('getId')->willReturn(1);

        $videosCollection = new ArrayCollection();

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->method('getVideos')->willReturn($videosCollection);

        $test = $this->getMockBuilder(Test::class)
            ->onlyMethods(['getModule'])
            ->getMock();
        $test->method('getModule')->willReturn($moduleMock);

        $this->assertFalse($test->videoBelongsToTest($videoMock));
    }
}