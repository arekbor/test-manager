<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Service;

interface AppSettingDecoderInterface
{
    public function decode(mixed $data): array;
    public function encode(array $value, string $className): mixed;
}