<?php

declare(strict_types = 1);

namespace App\Application\Test\Command;

use App\Application\Test\Model\TestModel;
use Symfony\Component\Uid\Uuid;

final class CreateTest
{
    private TestModel $testModel;
    private Uuid $creatorId;
    private Uuid $moduleId;

    public function __construct(
        TestModel $testModel,
        Uuid $creatorId,
        Uuid $moduleId
    ) {
        $this->testModel = $testModel;
        $this->creatorId = $creatorId;
        $this->moduleId = $moduleId;
    }

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