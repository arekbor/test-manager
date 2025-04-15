<?php

declare(strict_types = 1);

namespace App\Application\Question\Model;

use Symfony\Component\Uid\Uuid;

final class QuestionViewModel
{
    private Uuid $id;
    private Uuid $moduleId;
    private string $content;
    private int $answersCount;

    public function __construct(
        Uuid $id,
        Uuid $moduleId,
        string $content,
        int $answersCount
    ) {
        $this->id = $id;
        $this->moduleId = $moduleId;
        $this->content = $content;
        $this->answersCount = $answersCount;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAnswersCount(): int
    {
        return $this->answersCount;
    }
}