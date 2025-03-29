<?php

declare(strict_types = 1);

namespace App\Application\Test\Command;

use App\Domain\Model\TestSolve;
use Symfony\Component\Uid\Uuid;

final class ProcessTestSolve
{
    private Uuid $testId;
    private TestSolve $testSolve;

    public function __construct(
        Uuid $testId,
        TestSolve $testSolve
    ) {
        $this->testId = $testId;
        $this->testSolve = $testSolve;
    }

    public function getTestId(): Uuid
    {
        return $this->testId;
    }

    public function getTestSolve(): TestSolve
    {
        return $this->testSolve;
    }
}