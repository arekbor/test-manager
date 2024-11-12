<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\AppSetting;
use App\Repository\AppSettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;

class AppSettingService
{
    private const SERIALIZER_FORMAT = 'json';

    public function __construct(
        private AppSettingRepository $repository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    ) {
    }

    public function getValue(string $key, string $type): mixed
    {
        $appSetting = $this->getAppSetting($key);

        return $this->encode($appSetting->getValue(), $type);
    }

    public function setValue(string $key, mixed $data): void
    {
        $decodedData = $this->decode($data);

        $appSetting = new AppSetting();
        $appSetting->setKey($key)->setValue($decodedData);

        $this->em->persist($appSetting);
        $this->em->flush();
    }

    public function updateValue(string $key, mixed $data): void
    {
        $appSetting = $this->getAppSetting($key);
        $decodedData = $this->decode($data);

        $appSetting->setValue($decodedData);

        $this->em->persist($appSetting);
        $this->em->flush();
    }

    private function decode(mixed $data): array
    {
        $serializedData = $this->serializer->serialize($data, self::SERIALIZER_FORMAT);
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

        return $this->serializer->deserialize($encodedData, $type, self::SERIALIZER_FORMAT);
    }

    private function getAppSetting(string $key): AppSetting
    {
        $appSetting = $this->repository->findOneBy(['key' => $key]);
        if (empty($appSetting)) {
            throw new Exception("Entity with '{$key}' not found.");
        }

        return $appSetting;
    }
}   