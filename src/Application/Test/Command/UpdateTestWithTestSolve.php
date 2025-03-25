<?php

declare(strict_types = 1);

namespace App\Application\Test\Command;

use App\Domain\Entity\Test;
use App\Domain\Model\TestSolve;

final class UpdateTestWithTestSolve
{
    private Test $test;
    private \DateTimeInterface $start;
    private \DateTimeInterface $submission;
    private TestSolve $testSolve;

    public function __construct(
        Test $test,
        \DateTimeInterface $start,
        \DateTimeInterface $submission,
        TestSolve $testSolve
    ) {
        $this->test = $test;
        $this->start = $start;
        $this->submission = $submission;
        $this->testSolve = $testSolve;
    }

    public function getTest(): Test
    {
        return $this->test;
    }

    public function getStart(): \DateTimeInterface
    {
        return $this->start;
    }

    public function getSubmission(): \DateTimeInterface
    {
        return $this->submission;
    }

    public function getTestSolve(): TestSolve
    {
        return $this->testSolve;
    }
}