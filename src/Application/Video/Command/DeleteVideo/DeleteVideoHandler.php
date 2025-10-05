<?php

declare(strict_types=1);

namespace App\Application\Video\Command\DeleteVideo;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\Video;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteVideoHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

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
