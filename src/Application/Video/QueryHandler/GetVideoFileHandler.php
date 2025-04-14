<?php

declare(strict_types = 1);

namespace App\Application\Video\QueryHandler;

use App\Application\Shared\VichFileHandlerInterface;
use App\Application\Video\Query\GetVideoFile;
use App\Domain\Entity\Video;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetVideoFileHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VichFileHandlerInterface $vichFileHandler
    ) {
    }

    public function __invoke(GetVideoFile $query): \SplFileInfo
    {
        $videoId = $query->getVideoId();

        /**
         * @var Video $video
         */
        $video = $this->entityManager->find(Video::class, $videoId);
        if ($video === null) {
            throw new NotFoundException(Video::class, ['id' => $videoId]);
        }
 
        return $this->vichFileHandler->handle($video, Video::FILE_FIELD_NAME);
    }
}