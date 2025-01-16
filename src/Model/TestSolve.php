<?php 

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TestSolve
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

    #[Assert\LessThan('now')]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[Assert\Valid]
    private array $testQuestions;

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

    public function getTestQuestions(): array
    {
        return $this->testQuestions;
    }

    public function setTestQuestions(array $testQuestions): static
    {
        $this->testQuestions = $testQuestions;

        return $this;
    }
}