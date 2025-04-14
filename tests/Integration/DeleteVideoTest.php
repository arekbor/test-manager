<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Video\Command\DeleteVideo;
use App\Domain\Entity\Video;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class DeleteVideoTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('command.bus');
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testDeleteVideoCommandDeletesVideoSuccessfully(): void
    {
        //Arrange
        $testVideo = new Video();
        
        $this->entityManager->persist($testVideo);
        $this->entityManager->flush();

        $testVideoId = $testVideo->getId();

        $command = new DeleteVideo($testVideoId);

        //Act
        $this->commandBus->dispatch($command);

        //Assert
        /**
         * @var Video|null $video
         */
        $video = $this->entityManager->find(Video::class, $testVideoId);

        $this->assertNull($video);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testDeleteVideoCommandThrowsExceptionWhenVideoNotFound(): void
    {
        $notExistingVideoId = Uuid::v4();

        $command = new DeleteVideo($notExistingVideoId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Video {"id":"%s"}', $notExistingVideoId->toString()));

        $this->commandBus->dispatch($command);
    }
}