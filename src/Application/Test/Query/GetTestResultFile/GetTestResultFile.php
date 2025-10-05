<?php

declare(strict_types=1);

namespace App\Application\Test\Query\GetTestResultFile;

use App\Application\Shared\Bus\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class GetTestResultFile implements QueryInterface
{
    public function __construct(
        private readonly Uuid $testResultId
    ) {}

    public function getTestResultId(): Uuid
    {
        return $this->testResultId;
    }
}
