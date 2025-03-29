<?php

declare(strict_types = 1);

namespace App\Infrastructure\AppSetting\Service;

use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Domain\Entity\AppSetting;

final class AppSettingManager implements AppSettingManagerInterface
{
    public function __construct(
        private readonly AppSettingDecoder $appSettingDecoder
    ) {
    }

    public function create(string $key, mixed $value): AppSetting
    {
        $decodedValue = $this->appSettingDecoder->decode($value);

        $appSetting = new AppSetting();

        $appSetting->setKey($key);
        $appSetting->setValue($decodedValue);

        return $appSetting;
    }

    public function update(AppSetting $appSetting, mixed $newValue): AppSetting
    {
        $decodedValue = $this->appSettingDecoder->decode($newValue);

        $appSetting->setValue($decodedValue);

        return $appSetting;
    }

    public function get(AppSetting $appSetting, string $appSettingClassName): object
    {
        return $this->appSettingDecoder->encode($appSetting->getValue(), $appSettingClassName);
    }
}