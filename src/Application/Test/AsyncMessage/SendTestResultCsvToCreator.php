<?php

declare(strict_types=1);

namespace App\Application\Test\AsyncMessage;

use App\Application\Shared\Bus\AsyncMessageInterface;
use Symfony\Component\Uid\Uuid;

final class SendTestResultCsvToCreator implements AsyncMessageInterface
{
    public function __construct(
        private readonly Uuid $testId
    ) {}

    public function getTestId(): Uuid
    {
        return $this->testId;
    }
}
