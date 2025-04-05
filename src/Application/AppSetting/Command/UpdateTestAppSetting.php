<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Command;

use App\Application\AppSetting\Model\TestAppSetting;

final class UpdateTestAppSetting 
{
    private TestAppSetting $testAppSetting;

    public function __construct(
        TestAppSetting $testAppSetting
    ) {
        $this->testAppSetting = $testAppSetting;
    }

    public function getTestAppSetting(): TestAppSetting
    {
        return $this->testAppSetting;
    }
}