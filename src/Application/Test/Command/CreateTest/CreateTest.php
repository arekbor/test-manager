<?php

declare(strict_types=1);

namespace App\Application\Test\Command\CreateTest;

use App\Application\Shared\Bus\CommandInterface;
use App\Application\Test\Model\TestModel;
use Symfony\Component\Uid\Uuid;

final class CreateTest implements CommandInterface
{
    public function __construct(
        private readonly TestModel $testModel,
        private readonly Uuid $creatorId,
        private readonly Uuid $moduleId
    ) {}

    public function getTestModel(): TestModel
    {
        return $this->testModel;
    }

    public function getCreatorId(): Uuid
    {
        return $this->creatorId;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }
}
