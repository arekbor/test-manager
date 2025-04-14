<?php

declare(strict_types = 1);

namespace App\Application\Video\Command;

use Symfony\Component\Uid\Uuid;

final class DeleteVideo
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