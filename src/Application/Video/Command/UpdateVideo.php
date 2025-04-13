<?php

declare(strict_types = 1);

namespace App\Application\Video\Command;

use App\Application\Video\Model\UpdateVideoModel;
use Symfony\Component\Uid\Uuid;

final class UpdateVideo
{
    private Uuid $videoId;
    private UpdateVideoModel $updateVideoModel;

    public function __construct(
        Uuid $videoId,
        UpdateVideoModel $updateVideoModel
    ) {
        $this->videoId = $videoId;
        $this->updateVideoModel = $updateVideoModel;
    }

    public function getVideoId(): Uuid
    {
        return $this->videoId;
    }

    public function getUpdateVideoModel(): UpdateVideoModel
    {
        return $this->updateVideoModel;
    }
}