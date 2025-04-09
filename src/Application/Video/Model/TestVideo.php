<?php

declare(strict_types = 1);

namespace App\Application\Video\Model;

use Symfony\Component\Uid\Uuid;

final class TestVideo
{
    private Uuid $videoId;

    public function setVideoId(Uuid $videoId): static
    {
        $this->videoId = $videoId;

        return $this;
    }

    public function getVideoId(): Uuid
    {
        return $this->videoId;
    }
}