<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Service;

use App\Domain\Exception\JsonDecodeException;
use App\Domain\Exception\JsonEncodeException;
use Symfony\Component\Serializer\SerializerInterface;

final class AppSettingDecoder
{
    private const SERIALIZER_FORMAT = 'json';

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

    public function encode(array $value, string $className): mixed
    {
        $encodedData = json_encode($value);
        if (!$encodedData) {
            throw new JsonEncodeException();
        }

        return $this->serializer->deserialize($encodedData, $className, self::SERIALIZER_FORMAT);
    }
}