<?php 

declare(strict_types = 1);

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity()]
class Module extends BaseEntity
{
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $language = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\ManyToMany(targetEntity: Question::class, inversedBy: 'modules')]
    private Collection $questions;

    /**
     * @var Collection<int, Video>
     */
    #[ORM\ManyToMany(targetEntity: Video::class, mappedBy: 'modules')]
    private Collection $videos;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }
        
    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        $this->questions->removeElement($question);

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->addModule($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            $video->removeModule($this);
        }

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function findQuestionById(Uuid $id): ?Question
    {
        $questions = $this->getQuestions();

        $filtered = $questions->filter(
            fn(Question $q) => $q->getId()->equals($id)
        );

        return $filtered->first() ?: null;
    }
}
