<?php

declare(strict_types=1);

namespace App\Application\Test\Command\UpdateTest;

use App\Application\Shared\Bus\CommandInterface;
use App\Application\Test\Model\TestModel;
use Symfony\Component\Uid\Uuid;

final class UpdateTest implements CommandInterface
{
    public function __construct(
        private readonly Uuid $testId,
        private readonly TestModel $testModel
    ) {}

    public function getTestId(): Uuid
    {
        return $this->testId;
    }

    public function getTestModel(): TestModel
    {
        return $this->testModel;
    }
}
