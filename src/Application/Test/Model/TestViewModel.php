<?php

declare(strict_types = 1);

namespace App\Application\Test\Model;

use Symfony\Component\Uid\Uuid;

final class TestViewModel
{
    private Uuid $id;
    private Uuid $moduleId;
    private ?Uuid $testResultId;
    private string $moduleName;
    private string $moduleLanguage;
    private string $moduleCategory;
    private ?string $email;
    private ?string $firstname;
    private ?string $lastname;
    private ?string $workplace;
    private ?\DateTimeInterface $dateOfBirth;
    private \DateTimeInterface $expiration;
    private ?\DateTimeInterface $start;
    private ?\DateTimeInterface $submission;
    private ?int $score;

    public function __construct(
        Uuid $id,
        Uuid $moduleId,
        ?Uuid $testResultId,
        string $moduleName,
        string $moduleLanguage,
        string $moduleCategory,
        ?string $email,
        ?string $firstname,
        ?string $lastname,
        ?string $workplace,
        ?\DateTimeInterface $dateOfBirth,
        \DateTimeInterface $expiration,
        ?\DateTimeInterface $start,
        ?\DateTimeInterface $submission,
        ?int $score
    ) {
        $this->id = $id;
        $this->moduleId = $moduleId;
        $this->testResultId = $testResultId;
        $this->moduleName = $moduleName;
        $this->moduleLanguage = $moduleLanguage;
        $this->moduleCategory = $moduleCategory;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->workplace = $workplace;
        $this->dateOfBirth = $dateOfBirth;
        $this->expiration = $expiration;
        $this->start = $start;
        $this->submission = $submission;
        $this->score = $score;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getTestResultId(): ?Uuid
    {
        return $this->testResultId;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function getModuleLanguage(): string
    {
        return $this->moduleLanguage;
    }

    public function getModuleCategory(): string
    {
        return $this->moduleCategory;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getWorkplace(): ?string
    {
        return $this->workplace;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function getExpiration(): \DateTimeInterface
    {
        return $this->expiration;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function getSubmission(): ?\DateTimeInterface
    {
        return $this->submission;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }
}