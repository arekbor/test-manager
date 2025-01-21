<?php 

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Uid\Uuid;

class TestAnswerSolve
{
    private Uuid $answerId;

    private string $content;

    private bool $chosen;

    public function getAnswerId(): Uuid
    {
        return $this->answerId;
    }

    public function setAnswerId(Uuid $answerId): static
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