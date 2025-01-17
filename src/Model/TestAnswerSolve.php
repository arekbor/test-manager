<?php 

declare(strict_types=1);

namespace App\Model;

class TestAnswerSolve
{
    private int $answerId;

    private string $content;

    private bool $chosen;

    public function getAnswerId(): int
    {
        return $this->answerId;
    }

    public function setAnswerId(int $answerId): static
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

    public function isChosen(): bool
    {
        return $this->chosen;
    }

    public function setChosen(bool $chosen): static
    {
        $this->chosen = $chosen;

        return $this;
    }
}