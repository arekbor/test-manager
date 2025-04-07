<?php

declare(strict_types = 1);

namespace App\Application\Test\Command;

use App\Application\Test\Model\TestModel;
use Symfony\Component\Uid\Uuid;

final class UpdateTest
{
    private Uuid $testId;
    private TestModel $testModel;

    public function __construct(
        Uuid $testId,
        TestModel $testModel
    ) {
        $this->testId = $testId;
        $this->testModel = $testModel;
    }

    public function getTestId(): Uuid
    {
        return $this->testId;
    }

    public function getTestModel(): TestModel
    {
        return $this->testModel;
    }
}