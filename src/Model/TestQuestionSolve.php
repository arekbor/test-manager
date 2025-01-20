<?php 

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Uid\Uuid;

class TestQuestionSolve
{
    private Uuid $questionId;

    private string $content;

    private array $testAnswers;

    public function getQuestionId(): Uuid
    {
        return $this->questionId;
    }

    public function setQuestionId(Uuid $questionId): static
    {
        $this->questionId = $questionId;

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

    public function getTestAnswers(): array
    {
        return $this->testAnswers;
    }

    public function setTestAnswers(array $testAnswers): static
    {
        $this->testAnswers = $testAnswers;

        return $this;
    }
}