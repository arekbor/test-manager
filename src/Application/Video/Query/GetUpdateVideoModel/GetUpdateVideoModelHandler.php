<?php

declare(strict_types=1);

namespace App\Application\Video\Query\GetUpdateVideoModel;

use App\Application\Shared\Bus\QueryBusHandlerInterface;
use App\Application\Video\Model\UpdateVideoModel;
use App\Domain\Entity\Video;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class GetUpdateVideoModelHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(GetUpdateVideoModel $query): UpdateVideoModel
    {
        $videoId = $query->getVideoId();

        /**
         * @var Video|null $video
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
