<?php

declare(strict_types=1);

namespace App\Application\Test\Query\GetDataForTestSolve;

use App\Application\Shared\Bus\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class GetDataForTestSolve implements QueryInterface
{
    public function __construct(
        private readonly Uuid $testId
    ) {}

    public function getTestId(): Uuid
    {
        return $this->testId;
    }
}
