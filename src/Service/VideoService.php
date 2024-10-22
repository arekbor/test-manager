<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Video;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class VideoService
{
    public function upload(Video $video): void
    {
        throw new NotImplementedException("");
    }
}