<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Entity\Video;
use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Video\Model\UpdateVideoModel;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Video\Command\UpdateVideo\UpdateVideo;

final class UpdateVideoTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
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
        $this->commandBus->handle($command);

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

        $this->commandBus->handle($command);
    }
}
