<?php

declare(strict_types=1);

namespace App\Domain\Event;

use Symfony\Component\Uid\Uuid;

final class TestSolved
{
    private Uuid $testId;

    public function __construct(
        Uuid $testId
    ) {
        $this->testId = $testId;
    }

    public function getTestId(): Uuid
    {
        return $this->testId;
    }
}
