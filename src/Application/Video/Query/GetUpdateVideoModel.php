<?php

declare(strict_types = 1);

namespace App\Application\Video\Query;

use Symfony\Component\Uid\Uuid;

final class GetUpdateVideoModel
{
    private Uuid $videoId;

    public function __construct(
        Uuid $videoId
    ) {
        $this->videoId = $videoId;
    }

    public function getVideoId(): Uuid
    {
        return $this->videoId;
    }
}