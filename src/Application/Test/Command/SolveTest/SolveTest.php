<?php

declare(strict_types=1);

namespace App\Application\Test\Command\SolveTest;

use App\Application\Shared\Bus\CommandInterface;
use App\Application\Test\Model\TestSolve;
use Symfony\Component\Uid\Uuid;

final class SolveTest implements CommandInterface
{
    public function __construct(
        private readonly Uuid $testId,
        private readonly TestSolve $testSolve,
        private readonly \DateTimeInterface $start,
        private readonly \DateTimeInterface $submission,
    ) {}

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
