<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Service;

use App\Domain\Entity\AppSetting;

interface AppSettingManagerInterface 
{
    public function create(string $key, mixed $value): AppSetting;
    public function update(AppSetting $appSetting, mixed $newValue): AppSetting;
    public function get(AppSetting $appSetting, string $appSettingClassName): object;
}