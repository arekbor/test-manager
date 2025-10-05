<?php

declare(strict_types=1);

namespace App\Application\Video\Command\UpdateVideo;

use App\Application\Shared\Bus\CommandInterface;
use App\Application\Video\Model\UpdateVideoModel;
use Symfony\Component\Uid\Uuid;

final class UpdateVideo implements CommandInterface
{
    public function __construct(
        private readonly Uuid $videoId,
        private readonly UpdateVideoModel $updateVideoModel
    ) {}

    public function getVideoId(): Uuid
    {
        return $this->videoId;
    }

    public function getUpdateVideoModel(): UpdateVideoModel
    {
        return $this->updateVideoModel;
    }
}
