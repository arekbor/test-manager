<?php

declare(strict_types = 1);

namespace App\Application\Video\CommandHandler;

use App\Application\Video\Command\DeleteVideo;
use App\Domain\Entity\Video;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteVideoHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(DeleteVideo $command): void
    {
        $videoId = $command->getVideoId();

        /**
         * @var Video $video
         */
        $video = $this->entityManager->find(Video::class, $videoId);
        if ($video === null) {
            throw new NotFoundException(Video::class, ['id' => $videoId]);
        }

        $this->entityManager->remove($video);
    }
}