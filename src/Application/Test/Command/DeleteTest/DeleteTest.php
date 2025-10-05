<?php

declare(strict_types=1);

namespace App\Application\Test\Command\DeleteTest;

use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class DeleteTest implements CommandInterface
{
    public function __construct(
        private readonly Uuid $testId
    ) {}

    public function getTestId(): Uuid
    {
        return $this->testId;
    }
}
