<?php

declare(strict_types=1);

namespace App\Application\Video\Command\UpdateVideo;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\Video;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateVideoHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(UpdateVideo $command): void
    {
        $videoId = $command->getVideoId();

        /**
         * @var Video|null $video
         */
        $video = $this->entityManager->find(Video::class, $videoId);
        if ($video === null) {
            throw new NotFoundException(Video::class, ['id' => $videoId]);
        }

        $updateVideoModel = $command->getUpdateVideoModel();

        $video->setOriginalName($updateVideoModel->getOriginalName());
    }
}
