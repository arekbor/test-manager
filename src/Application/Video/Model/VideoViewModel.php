<?php

declare(strict_types = 1);

namespace App\Application\Video\Model;

use Symfony\Component\Uid\Uuid;

final class VideoViewModel
{
    private Uuid $id;
    private Uuid $moduleId;
    private string $originalName;
    private string $mimeType;
    private string $size;

    public function __construct(
        Uuid $id,
        Uuid $moduleId,
        string $originalName,
        string $mimeType,
        string $size
    ) {
        $this->id = $id;
        $this->moduleId = $moduleId;
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->size = $size;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): string
    {
        return $this->size;
    }
}