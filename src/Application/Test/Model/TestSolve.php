<?php 

declare(strict_types = 1);

namespace App\Application\Test\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

final class TestSolve
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 100)]
    private ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 100)]
    private ?string $lastname = null;

    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 100)]
    private ?string $workplace = null;

    #[Assert\LessThan('today')]
    private ?\DateTimeInterface $dateOfBirth = null;

    /**
     * @var Collection<int, TestQuestionSolve>
     */
    #[Assert\Valid]
    private Collection $testQuestionSolves;

    #[Assert\IsTrue]
    private bool $consent;

    public function __construct() 
    {
        $this->testQuestionSolves = new ArrayCollection();
        $this->consent = false;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getWorkplace(): ?string
    {
        return $this->workplace;
    }

    public function setWorkplace(?string $workplace): static
    {
        $this->workplace = $workplace;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @var Collection<int, TestQuestionSolve>
     */
    public function getTestQuestionSolves(): Collection
    {
        return $this->testQuestionSolves;
    }

    public function addTestQuestionSolve(TestQuestionSolve $testQuestionSolve): static
    {
        if (!$this->testQuestionSolves->contains($testQuestionSolve)) {
            $this->testQuestionSolves->add($testQuestionSolve);
        }

        return $this;
    }

    public function removeTestQuestionSolve(TestQuestionSolve $testQuestionSolve): static
    {
        $this->testQuestionSolves->removeElement($testQuestionSolve);

        return $this;
    }

    public function isConsent(): bool
    {
        return $this->consent;
    }

    public function setConsent(bool $consent): static
    {
        $this->consent = $consent;

        return $this;
    }
}