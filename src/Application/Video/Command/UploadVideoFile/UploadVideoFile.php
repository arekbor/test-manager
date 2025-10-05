<?php

declare(strict_types=1);

namespace App\Application\Video\Command\UploadVideoFile;

use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class UploadVideoFile implements CommandInterface
{
    public function __construct(
        private readonly \SplFileInfo $uploadedFile,
        private readonly Uuid $moduleId
    ) {}

    public function getUploadedFile(): \SplFileInfo
    {
        return $this->uploadedFile;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }
}
