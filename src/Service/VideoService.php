<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Module;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Uid\Uuid;

class VideoService
{
    public function __construct(
        private ParameterBagInterface $params,
        private FileService $fileService,
        private EntityManagerInterface $em,
    ) {
    }

    public function upload(UploadedFile $file, Module $module): void
    {
        $uuid = Uuid::v7()->toString();
        $basePath = $this->getBasePath();
    
        $file = $this->fileService->save($file, $basePath, $uuid);

        $video = new Video();
        $video->setFilename($file->getFilename());
        $video->addModule($module);

        $this->em->persist($video);
        $this->em->flush();
    }

    public function deleteVideo(Video $video): void
    {
        $filename = $video->getFilename();
        $basePath = $this->getBasePath();

        if (!$this->fileService->delete($basePath, $filename)) {
            throw new Exception("Failed to delete the file: {$filename}");
        }

        $this->em->remove($video);
        $this->em->flush();
    }

    public function getVideoFile(Video $video): File
    {
        $filename = $video->getFilename();
        $basePath = $this->getBasePath();

        return $this->fileService->getFile($basePath, $filename);
    }

    private function getBasePath(): string
    {
        return $this->params->get('app.video.upload.base.path');
    }
}