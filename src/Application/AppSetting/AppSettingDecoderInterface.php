<?php

declare(strict_types = 1);

namespace App\Application\AppSetting;

interface AppSettingDecoderInterface
{
    public const SERIALIZER_FORMAT = 'json';
    public function decode(mixed $data): array;
}