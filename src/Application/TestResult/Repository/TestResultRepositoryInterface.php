<?php

declare(strict_types = 1);

namespace App\Application\TestResult\Repository;

use App\Domain\Entity\TestResult;

interface TestResultRepositoryInterface
{
    public function persistTestResult(TestResult $testResult): void;
}