<?php

declare(strict_types = 1);

namespace App\Application\Test\Command;

use App\Application\Test\Model\TestModel;
use Symfony\Component\Uid\Uuid;

final class CreateTest
{
    private TestModel $testModel;
    private string $creatorEmail;
    private Uuid $moduleId;

    public function __construct(
        TestModel $testModel,
        string $creatorEmail,
        Uuid $moduleId
    ) {
        $this->testModel = $testModel;
        $this->creatorEmail = $creatorEmail;
        $this->moduleId = $moduleId;
    }

    public function getTestModel(): TestModel
    {
        return $this->testModel;
    }
    
    public function getCreatorEmail(): string
    {
        return $this->creatorEmail;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }
}