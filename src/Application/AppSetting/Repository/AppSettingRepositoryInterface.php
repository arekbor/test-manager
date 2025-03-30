<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Repository;

use App\Domain\Entity\AppSetting;

interface AppSettingRepositoryInterface
{
    public function getByKey(string $appSettingKey): ?AppSetting;
}