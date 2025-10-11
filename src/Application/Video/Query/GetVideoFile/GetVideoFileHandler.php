<?php

declare(strict_types=1);

namespace App\Application\Video\Query\GetVideoFile;

use App\Application\Shared\Bus\QueryBusHandlerInterface;
use App\Application\Shared\VichFileHandlerInterface;
use App\Domain\Entity\Video;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class GetVideoFileHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VichFileHandlerInterface $vichFileHandler
    ) {}

    public function __invoke(GetVideoFile $query): \SplFileInfo
    {
        $videoId = $query->getVideoId();

        /**
         * @var Video|null $video
         */
        $video = $this->entityManager->find(Video::class, $videoId);
        if ($video === null) {
            throw new NotFoundException(Video::class, ['id' => $videoId]);
        }

        return $this->vichFileHandler->handle($video, Video::FILE_FIELD_NAME);
    }
}
