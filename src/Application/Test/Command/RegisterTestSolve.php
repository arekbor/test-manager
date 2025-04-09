<?php

declare(strict_types = 1);

namespace App\Application\Test\Command;

use App\Application\Test\Model\TestSolve;
use Symfony\Component\Uid\Uuid;

final class RegisterTestSolve
{
    private Uuid $testId;
    private TestSolve $testSolve;
    private \DateTimeInterface $start;
    private \DateTimeInterface $submission;
    
    public function __construct(
        Uuid $testId,
        TestSolve $testSolve,
        \DateTimeInterface $start,
        \DateTimeInterface $submission,
    ) {
        $this->testId = $testId;
        $this->testSolve = $testSolve;
        $this->start = $start;
        $this->submission = $submission;
    }

    public function getTestId(): Uuid
    {
        return $this->testId;
    }

    public function getTestSolve(): TestSolve
    {
        return $this->testSolve;
    }

    public function getStart(): \DateTimeInterface
    {
        return $this->start;
    }

    public function getSubmission(): \DateTimeInterface
    {
        return $this->submission;
    }
}