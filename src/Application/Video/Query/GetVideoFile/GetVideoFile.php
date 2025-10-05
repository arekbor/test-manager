<?php

declare(strict_types=1);

namespace App\Application\Video\Query\GetVideoFile;

use App\Application\Shared\Bus\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class GetVideoFile implements QueryInterface
{
    public function __construct(
        private readonly Uuid $videoId
    ) {}

    public function getVideoId(): Uuid
    {
        return $this->videoId;
    }
}
