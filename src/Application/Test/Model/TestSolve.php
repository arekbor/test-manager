<?php

declare(strict_types=1);

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
     * @var Collection<int, TestQuestionSolve> $testQuestionSolves
     */
    #[Assert\Valid]
    private Collection $testQuestionSolves;

    #[Assert\IsTrue]
    private bool $privacyPolicyConsent;

    public function __construct()
    {
        $this->testQuestionSolves = new ArrayCollection();
        $this->privacyPolicyConsent = false;
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
     * @return ArrayCollection<int, TestQuestionSolve>
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

    public function isPrivacyPolicyConsent(): bool
    {
        return $this->privacyPolicyConsent;
    }

    public function setPrivacyPolicyConsent(bool $privacyPolicyConsent): static
    {
        $this->privacyPolicyConsent = $privacyPolicyConsent;

        return $this;
    }
}
