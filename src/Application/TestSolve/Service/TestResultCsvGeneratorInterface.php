<?php

declare(strict_types = 1);

namespace App\Application\TestSolve\Service;

use App\Domain\Entity\Test;

interface TestResultCsvGeneratorInterface
{
    public function create(Test $test): \SplFileInfo;
}