<?php

declare(strict_types = 1);

namespace App\Application\Answer\Model;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class AnswerModel
{
    private ?Uuid $answerId = null;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 255)]
    private string $content;

    private bool $correct;

    public function getAnswerId(): ?Uuid
    {
        return $this->answerId;
    }

    public function setAnswerId(?Uuid $answerId): static
    {
        $this->answerId = $answerId;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function setCorrect(bool $correct): static
    {
        $this->correct = $correct;

        return $this;
    }
}