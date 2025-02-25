<?php

declare(strict_types=1);

namespace App\Message\Event;


use App\Model\TestSolve;
use Symfony\Component\Uid\Uuid;

class GenerateTestResult
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