<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\DTO;

final class AppSettingToCreate
{
    private string $key;
    private mixed $value;

    public function __construct(string $key, mixed $value) {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}