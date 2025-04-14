<?php

declare(strict_types = 1);

namespace App\Application\Video\Command;

use Symfony\Component\Uid\Uuid;

final class UploadVideoFile
{
    private \SplFileInfo $uploadedFile;
    private Uuid $moduleId;

    public function __construct(
        \SplFileInfo $uploadedFile,
        Uuid $moduleId
    ) {
        $this->uploadedFile = $uploadedFile;
        $this->moduleId = $moduleId;
    }

    public function getUploadedFile(): \SplFileInfo
    {
        return $this->uploadedFile;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }
}