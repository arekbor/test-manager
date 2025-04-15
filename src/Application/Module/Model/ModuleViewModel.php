<?php

declare(strict_types = 1);

namespace App\Application\Module\Model;

use Symfony\Component\Uid\Uuid;

final class ModuleViewModel
{
    private Uuid $id;
    private string $name;
    private string $language;
    private string $category;
    private int $questionsCount;
    private int $videosCount;

    public function __construct(
        Uuid $id,
        string $name,
        string $language,
        string $category,
        int $questionsCount,
        int $videosCount
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->language = $language;
        $this->category = $category;
        $this->questionsCount = $questionsCount;
        $this->videosCount = $videosCount;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getQuestionsCount(): int
    {
        return $this->questionsCount;
    }

    public function getVideosCount(): int
    {
        return $this->videosCount;
    }
}