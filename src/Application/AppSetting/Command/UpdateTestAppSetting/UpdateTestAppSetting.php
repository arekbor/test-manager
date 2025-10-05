<?php

declare(strict_types=1);

namespace App\Application\AppSetting\Command\UpdateTestAppSetting;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\Shared\Bus\CommandInterface;

final class UpdateTestAppSetting implements CommandInterface
{
    public function __construct(
        private readonly TestAppSetting $testAppSetting
    ) {}

    public function getTestAppSetting(): TestAppSetting
    {
        return $this->testAppSetting;
    }
}
