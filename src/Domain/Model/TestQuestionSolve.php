<?php 

declare(strict_types=1);

namespace App\Domain\Model;

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

    public function extractChosenAnswerIds(): array
    {
        $chosenAnswers = array_filter(
            $this->testAnswers ?? [],
            fn(TestAnswerSolve $t) => $t->isChosen()
        );

        $chosenAnswerIds = array_map(
            fn(TestAnswerSolve $t) => $t->getAnswerId()->toRfc4122(),
            $chosenAnswers
        );

        return array_values($chosenAnswerIds);
    }
}