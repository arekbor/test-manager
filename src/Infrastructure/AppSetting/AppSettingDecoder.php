<?php

declare(strict_types = 1);

namespace App\Infrastructure\AppSetting;

use App\Application\AppSetting\AppSettingDecoderInterface;
use App\Domain\Exception\JsonDecodeException;
use Symfony\Component\Serializer\SerializerInterface;

final class AppSettingDecoder implements AppSettingDecoderInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    public function decode(mixed $data): array
    {
        $serializedData = $this->serializer->serialize($data, self::SERIALIZER_FORMAT);

        $decodedData = json_decode($serializedData, true);
        if (!$decodedData) {
            throw new JsonDecodeException();
        }

        return $decodedData;
    }
}