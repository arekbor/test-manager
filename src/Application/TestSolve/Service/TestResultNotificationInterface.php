<?php

declare(strict_types = 1);

namespace App\Application\TestSolve\Service;

use App\Domain\Entity\Test;

interface TestResultNotificationInterface
{
    public function send(Test $test): void;
}