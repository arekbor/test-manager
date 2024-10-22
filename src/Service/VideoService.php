<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Video;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VideoService
{
    public function __construct(
        private ParameterBagInterface $params
    ) {
    }

    public function saveToDisk(Video $video): void
    {
        $dir = $this->params->get('app.video.upload.directory.path');
        if (empty($dir)) {
            throw new Exception("Missing directory path for video uploads.");
        }

        if (!is_dir($dir)) {
            throw new Exception("Upload directory does not exist.");
        }

        if (!is_writable($dir)) {
            throw new Exception("Upload directory is not writable.");
        }

        if (file_exists($dir . DIRECTORY_SEPARATOR . $video->getFilename())) {
            throw new Exception("File already exists.");
        }

        $video->getFile()->move($dir, $video->getFilename());
    }
}