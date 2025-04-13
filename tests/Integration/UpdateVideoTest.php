<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Video\Command\UpdateVideo;
use App\Application\Video\Model\UpdateVideoModel;
use App\Domain\Entity\Video;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateVideoTest extends DatabaseTestCase
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
    public function testUpdateVideoCommandUpdatesVideoSuccessfully(): void
    {
        //Arrange
        $testVideo = new Video();
        $testVideo->setOriginalName('video.mp4');

        $this->entityManager->persist($testVideo);
        $this->entityManager->flush();

        $updateVideoModel = new UpdateVideoModel();
        $updateVideoModel->setOriginalName('Test video');

        $command = new UpdateVideo($testVideo->getId(), $updateVideoModel);

        //Act
        $this->commandBus->dispatch($command);

        /**
         * @var Video $video
         */
        $video = $this->entityManager->find(Video::class, $testVideo->getId());

        //Assert
        $this->assertInstanceOf(Video::class, $video);
        $this->assertEquals($testVideo->getId(), $video->getId());
        $this->assertEquals('Test video', $video->getOriginalName());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateVideoCommandThrowsExceptionWhenVideoNoFound(): void
    {
        $notExistingVideoId = Uuid::v4();

        $command = new UpdateVideo($notExistingVideoId, new UpdateVideoModel());

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Video {"id":"%s"}', $notExistingVideoId->toString()));

        $this->commandBus->dispatch($command);
    }
}