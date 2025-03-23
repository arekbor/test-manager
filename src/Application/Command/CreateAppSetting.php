<?php

declare(strict_types = 1);

namespace App\Application\Command;

final class CreateAppSetting
{
    private string $keyAppSetting;
    private mixed $value;

    public function __construct(
        string $keyAppSetting, mixed $value
    ) {
        $this->keyAppSetting = $keyAppSetting;
        $this->value = $value;
    }

    public function getKeyAppSetting(): string
    {
        return $this->keyAppSetting;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}