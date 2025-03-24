<?php

declare(strict_types = 1);

namespace App\Infrastructure\TestResult\Repository;

use App\Application\TestResult\Repository\TestResultRepositoryInterface;
use App\Domain\Entity\TestResult;
use App\Infrastructure\Shared\AbstractRepository;

final class TestResultRepository extends AbstractRepository implements TestResultRepositoryInterface 
{
    public function persistTestResult(TestResult $testResult): void
    {
        $this->persist($testResult);
    }
}