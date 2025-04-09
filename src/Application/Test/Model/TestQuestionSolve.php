<?php 

declare(strict_types = 1);

namespace App\Application\Test\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

final class TestQuestionSolve
{
    private Uuid $questionId;

    private string $content;

    /**
     * @var Collection<int, TestAnswerSolve>
     */
    private Collection $testAnswerSolves;

    public function __construct() 
    {
        $this->testAnswerSolves = new ArrayCollection();
    }

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

    /**
     * @var Collection<int, TestAnswerSolve>
     */
    public function getTestAnswerSolves(): Collection
    {
        return $this->testAnswerSolves;
    }

    public function addTestAnswerSolve(TestAnswerSolve $testAnswerSolve): static
    {
        if (!$this->testAnswerSolves->contains($testAnswerSolve)) {
            $this->testAnswerSolves->add($testAnswerSolve);
        }

        return $this;
    }

    public function removeTestAnswerSolve(TestAnswerSolve $testAnswerSolve): static
    {
        $this->testAnswerSolves->removeElement($testAnswerSolve);

        return $this;
    }

    public function extractChosenAnswerIds(): array
    {
        $chosenAnswers = array_filter($this->testAnswerSolves->toArray() ?? [], fn(TestAnswerSolve $t) => $t->isChosen());

        $chosenAnswerIds = array_map(fn(TestAnswerSolve $t) => $t->getAnswerId()->toRfc4122(), $chosenAnswers);

        return array_values($chosenAnswerIds);
    }
}