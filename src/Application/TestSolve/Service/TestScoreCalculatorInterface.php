<?php

declare(strict_types = 1);

namespace App\Application\TestSolve\Service;

use App\Domain\Entity\Test;
use App\Domain\Model\TestSolve;

interface TestScoreCalculatorInterface
{
    public function calculate(TestSolve $testSolve, Test $test): int;
}