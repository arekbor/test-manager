<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\AppSetting;
use App\Repository\AppSettingRepository;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;

class AppSettingService
{
    private const SERIALIZER_FORMAT = 'json';

    public function __construct(
        private AppSettingRepository $appSettingRepository,
        private SerializerInterface $serializer
    ) {
    }

    public function getValue(string $key, string $type): mixed
    {
        $appSetting = $this->getAppSetting($key);
        $value = $appSetting->getValue();

        return $this->encode($value, $type);
    }

    public function setValue(string $key, mixed $data): AppSetting
    {
        $decodedData = $this->decode($data);

        $appSetting = new AppSetting();
        $appSetting
            ->setKey($key)
            ->setValue($decodedData)
        ;

        return $appSetting;
    }

    public function updateValue(string $key, mixed $data): AppSetting
    {
        $appSetting = $this->getAppSetting($key);
        $decodedData = $this->decode($data);

        $appSetting->setValue($decodedData);

        return $appSetting;
    }

    private function decode(mixed $data): array
    {
        $serializedData = $this
            ->serializer
            ->serialize($data, self::SERIALIZER_FORMAT)
        ;

        $decodedData = json_decode($serializedData, true);
        if (!$decodedData) {
            throw new Exception("Failed to decode serialized value.");
        }

        return $decodedData;
    }

    private function encode(mixed $data, string $type): mixed
    {
        $encodedData = json_encode($data);
        if (!$encodedData) {
            throw new Exception("Failed to encode value to JSON.");
        }

        return $this
            ->serializer
            ->deserialize($encodedData, $type, self::SERIALIZER_FORMAT)
        ;
    }

    private function getAppSetting(string $key): AppSetting
    {
        $appSetting = $this
            ->appSettingRepository
            ->findByKey($key)
        ;

        if (empty($appSetting)) {
            throw new Exception(AppSetting::class . "with " . $key . " not found.");
        }

        return $appSetting;
    }
}   