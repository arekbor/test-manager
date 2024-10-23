<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Video;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoService
{
    private string $uploadDirectory;

    public function __construct(
        private ParameterBagInterface $params
    ) {
        $this->uploadDirectory = $this->params->get('app.video.upload.directory.path');
        if (empty($this->uploadDirectory)) {
            throw new Exception("Missing directory path for video uploads.");
        }
    }

    public function uploadFile(Video $video): void
    {
        $this->validateUploadDirectory();

        if (file_exists($this->getFullPath($video))) {
            throw new Exception("File already exists.");
        }

        $video->getFile()->move($this->uploadDirectory, $video->getFilename());
    }

    public function getFile(Video $video): UploadedFile
    {
        return new UploadedFile($this->getFullPath($video), $video->getFilename());
    }

    private function getFullPath(Video $video): string {
        $filename = $video->getFilename();
        if (empty($filename)) {
            throw new Exception("Filename not found");
        }

        return $this->uploadDirectory . DIRECTORY_SEPARATOR . $filename;
    }

    private function validateUploadDirectory(): void
    {
        if (!is_dir($this->uploadDirectory)) {
            throw new Exception("Upload directory does not exist.");
        }

        if (!is_writable($this->uploadDirectory)) {
            throw new Exception("Upload directory is not writable.");
        }
    }
}