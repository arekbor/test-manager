<?php

declare(strict_types = 1);

namespace App\Application\Test\Command;

use App\Domain\Entity\Test;
use App\Application\Test\Model\TestSolve;

final class RegisterTestSolve
{
    private Test $test;
    private TestSolve $testSolve;
    private \DateTimeInterface $start;
    private \DateTimeInterface $submission;
    
    public function __construct(
        Test $test,
        TestSolve $testSolve,
        \DateTimeInterface $start,
        \DateTimeInterface $submission,
    ) {
        $this->test = $test;
        $this->testSolve = $testSolve;
        $this->start = $start;
        $this->submission = $submission;
    }

    public function getTest(): Test
    {
        return $this->test;
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