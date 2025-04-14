<?php

declare(strict_types = 1);

namespace App\Application\Test\Query;

use Symfony\Component\Uid\Uuid;

final class GetTestResultFile
{
    private Uuid $testResultId;

    public function __construct(
        Uuid $testResultId
    ) {
        $this->testResultId = $testResultId;
    }

    public function getTestResultId(): Uuid
    {
        return $this->testResultId;
    }
} 