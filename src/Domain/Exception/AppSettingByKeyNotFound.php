<?php

declare(strict_types = 1);

namespace App\Domain\Exception;

use App\Domain\Entity\AppSetting;

final class AppSettingByKeyNotFound extends \Exception
{
    public function __construct(string $key) {
        
        parent::__construct(sprintf("%s not found by key %s.", AppSetting::class, $key));
    }
}