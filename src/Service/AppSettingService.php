<?php 

declare(strict_types=1);

namespace App\Service;

use App\Entity\AppSetting;
use App\Exception\JsonDecodeException;
use App\Exception\JsonEncodeException;
use Symfony\Component\Serializer\SerializerInterface;

class AppSettingService
{
    private const SERIALIZER_FORMAT = 'json';

    public function __construct(
        private SerializerInterface $serializer
    ) {
    }

    public function setValue(string $key, mixed $data): AppSetting
    {
        $decodedData = $this->serializeAndDecode($data);

        $appSetting = new AppSetting();
        $appSetting
            ->setKey($key)
            ->setValue($decodedData)
        ;

        return $appSetting;
    }

    public function updateValue(AppSetting $appSetting, mixed $data): AppSetting
    {
        $decodedData = $this->serializeAndDecode($data);

        $appSetting
            ->setValue($decodedData)
        ;

        return $appSetting;
    }

    public function getValue(AppSetting $appSetting, string $classType): mixed
    {
        $value = $appSetting->getValue();

        $encodedData = json_encode($value);
        if (!$encodedData) {
            throw new JsonEncodeException();
        }

        return $this
            ->serializer
            ->deserialize($encodedData, $classType, self::SERIALIZER_FORMAT)
        ;
    }

    private function serializeAndDecode(mixed $data): array
    {
        $serializedData = $this
            ->serializer
            ->serialize($data, self::SERIALIZER_FORMAT)
        ;

        $decodedData = json_decode($serializedData, true);
        if (!$decodedData) {
            throw new JsonDecodeException();
        }

        return $decodedData;
    }
}   