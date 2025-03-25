<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Repository;

use App\Application\Shared\RepositoryInterface;
use App\Domain\Entity\AppSetting;

interface AppSettingRepositoryInterface extends RepositoryInterface
{
    public function getByKey(string $appSettingKey): ?AppSetting;
}