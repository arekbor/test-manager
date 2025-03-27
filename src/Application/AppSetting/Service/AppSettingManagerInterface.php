<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Service;

use App\Application\AppSetting\DTO\AppSettingToCreate;

interface AppSettingManagerInterface 
{
    public function createMany(AppSettingToCreate ...$appSettings): void;
    public function get(string $key, string $appSettingClassName): object;
    public function update(string $key, mixed $newValue): void;
}