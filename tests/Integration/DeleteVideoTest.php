<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Entity\Video;
use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Video\Command\DeleteVideo\DeleteVideo;

final class DeleteVideoTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
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
        $this->commandBus->handle($command);

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

        $this->commandBus->handle($command);
    }
}
