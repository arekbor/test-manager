<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Service;

interface AppSettingDecoderInterface
{
    public const SERIALIZER_FORMAT = 'json';

    /**
     * @return array<string, mixed>
     */
    public function decode(mixed $data): array;

    /**
     * @param array<string, mixed> $value
     */
    public function encode(array $value, string $className): mixed;
}