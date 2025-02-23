<?php 

declare(strict_types=1);

namespace App\Model;

use App\Entity\Test;
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

    #[Assert\LessThan('today')]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[Assert\Valid]
    private array $testQuestions;

    #[Assert\IsTrue]
    private bool $consent;

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

    public function isConsent(): bool
    {
        return $this->consent;
    }

    public function setConsent(bool $consent): static
    {
        $this->consent = $consent;

        return $this;
    }

    public function calculateScore(Test $test): int
    {
        $score = 0;

        foreach($this->getTestQuestions() as $testQuestionSolve) {
            $testQuestionSolveId = $testQuestionSolve->getQuestionId();
            $question = $test->getModule()->findQuestionById($testQuestionSolveId);
            if (!$question) { 
                continue; 
            }

            $chosenAnswerIds = $testQuestionSolve->extractChosenAnswerIds();
            if (!empty($chosenAnswerIds) && $question->chosenAnswersCorrect($chosenAnswerIds)) {
                $score++;
            }
        }

        return $score;
    }
}