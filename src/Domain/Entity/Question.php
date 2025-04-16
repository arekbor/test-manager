<?php 

declare(strict_types = 1);

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity()]
class Question extends BaseEntity
{
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $answers;

    /**
     * @var Collection<int, Module>
     */
    #[ORM\ManyToMany(targetEntity: Module::class, mappedBy: 'questions')]
    private Collection $modules;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->modules = new ArrayCollection();
    }
    
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): static
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->addQuestion($this);
        }

        return $this;
    }

    public function removeModule(Module $module): static
    {
        if ($this->modules->removeElement($module)) {
            $module->removeQuestion($this);
        }

        return $this;
    }

    public function updateAnswerPositions(): static
    {
        $position = 1;

        foreach($this->answers as $answer) {
            $answer->setPosition($position++);
        }

        return $this;
    }

    public function extractCorrectAnswerIds(): array
    {
        $answers = $this->answers->toArray();

        $correctAnswers = array_filter(
            $answers,
            fn(Answer $a) => $a->isCorrect()
        );

        $correctAnswerIds = array_map(
            fn(Answer $a) => $a->getId()->toRfc4122(),
            $correctAnswers
        );

        return array_values($correctAnswerIds);
    }

    public function chosenAnswersCorrect(array $chosenAnswerIds): bool
    {
        $correctAnswerIds = $this->extractCorrectAnswerIds();

        sort($correctAnswerIds);
        sort($chosenAnswerIds);

        return !empty($correctAnswerIds) && !empty($chosenAnswerIds) && $chosenAnswerIds === $correctAnswerIds;
    }

    public function getAnswerById(?Uuid $answerId): ?Answer
    {
        return $this->answers
            ->filter(fn(Answer $a) => $a->getId() && $answerId && $a->getId()->equals($answerId))
            ->first() ?: null;
    }
}
