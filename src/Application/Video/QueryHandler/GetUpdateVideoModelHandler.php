<?php

declare(strict_types = 1);

namespace App\Application\Video\QueryHandler;

use App\Application\Video\Model\UpdateVideoModel;
use App\Application\Video\Query\GetUpdateVideoModel;
use App\Domain\Entity\Video;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetUpdateVideoModelHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(GetUpdateVideoModel $query): UpdateVideoModel
    {
        $videoId = $query->getVideoId();

        /**
         * @var Video $video
         */
        $video = $this->entityManager->find(Video::class, $videoId);
        if ($video === null) {
            throw new NotFoundException(Video::class, ['id' => $videoId]);
        }

        $updateVideoModel = new UpdateVideoModel();
        $updateVideoModel->setOriginalName($video->getOriginalName());

        return $updateVideoModel;
    }
}