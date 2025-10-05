<?php

declare(strict_types=1);

namespace App\Application\Video\Query\GetUpdateVideoModel;

use App\Application\Shared\Bus\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class GetUpdateVideoModel implements QueryInterface
{
    public function __construct(
        private readonly Uuid $videoId
    ) {}

    public function getVideoId(): Uuid
    {
        return $this->videoId;
    }
}
