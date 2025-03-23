<?php

declare(strict_types=1);

namespace App\Application\TestSolve\Command;

use App\Domain\Model\TestSolve;
use Symfony\Component\Uid\Uuid;

final class CreateTestSolve
{
    public function __construct(
        private TestSolve $testSolve,
        private Uuid $testId
    ) {
    }

    public function getTestSolve(): TestSolve
    {
        return $this->testSolve;
    }

    public function getTestId(): Uuid
    {
        return $this->testId;
    }
}