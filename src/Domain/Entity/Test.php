<?php 

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Repository\TestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test extends BaseEntity
{
    #[ORM\Column(type: 'default_datetime_tz', nullable: true)]
    #[Assert\GreaterThanOrEqual('now')]
    private ?\DateTimeInterface $expiration = null;

    #[ORM\Column(type: 'default_datetime_tz', nullable: true)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: 'default_datetime_tz', nullable: true)]
    private ?\DateTimeInterface $submission = null;

    #[ORM\ManyToOne]
    private ?Module $module = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $workplace = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\OneToOne(inversedBy: 'test', cascade: ['persist', 'remove'])]
    private ?TestResult $testResult = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecurityUser $creator = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    public function getExpiration(): ?\DateTimeInterface
    {
        return $this->expiration;
    }

    public function setExpiration(?\DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getSubmission(): ?\DateTimeInterface
    {
        return $this->submission;
    }

    public function setSubmission(?\DateTimeInterface $submission): static
    {
        $this->submission = $submission;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }
    
    public function isValid(): bool
    {
        $now = new \DateTime();

        return $now < $this->expiration && $this->submission === null;
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

    public function getTestResult(): ?TestResult
    {
        return $this->testResult;
    }

    public function setTestResult(?TestResult $testResult): static
    {
        $this->testResult = $testResult;

        return $this;
    }

    public function getCreator(): ?SecurityUser
    {
        return $this->creator;
    }

    public function setCreator(?SecurityUser $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }
}