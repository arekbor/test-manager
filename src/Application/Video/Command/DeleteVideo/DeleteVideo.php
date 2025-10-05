<?php

declare(strict_types=1);

namespace App\Application\Video\Command\DeleteVideo;

use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class DeleteVideo implements CommandInterface
{
    public function __construct(
        private readonly Uuid $videoId
    ) {}

    public function getVideoId(): Uuid
    {
        return $this->videoId;
    }
}
