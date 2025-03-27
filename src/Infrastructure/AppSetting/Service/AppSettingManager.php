<?php

declare(strict_types = 1);

namespace App\Infrastructure\AppSetting\Service;

use App\Application\AppSetting\DTO\AppSettingToCreate;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\UnitOfWorkInterface;
use App\Domain\Entity\AppSetting;
use App\Domain\Exception\NotFoundException;

final class AppSettingManager implements AppSettingManagerInterface
{
    public function __construct(
        private readonly AppSettingDecoder $appSettingDecoder,
        private readonly AppSettingRepositoryInterface $appSettingRepository,
        private readonly UnitOfWorkInterface $unitOfWork
    ) {
    }

    public function createMany(AppSettingToCreate ...$appSettings): void
    {
        /**
         * @var AppSettingToCreate $appSettingToCreate
         */
        foreach($appSettings as $appSettingToCreate) {
            $decodedValue = $this->appSettingDecoder->decode($appSettingToCreate->getValue());

            $appSetting = new AppSetting();

            $appSetting->setKey($appSettingToCreate->getKey());
            $appSetting->setValue($decodedValue);

            $this->appSettingRepository->persist($appSetting);
        }

        $this->unitOfWork->commit();
    }

    public function get(string $key, string $appSettingClassName): object
    {
        $appSetting = $this->appSettingRepository->getByKey($key);
        if (!$appSetting) {
            throw new NotFoundException(AppSetting::class, ['key' => $key]);
        }

        return $this->appSettingDecoder->encode($appSetting->getValue(), $appSettingClassName);
    }

    public function update(string $key, mixed $newValue): void
    {
        /**
         * @var AppSetting $appSetting
         */
        $appSetting = $this->appSettingRepository->getByKey($key);
        if (!$appSetting) {
            throw new NotFoundException(AppSetting::class, ['key' => $key]);
        }

        $decodedValue = $this->appSettingDecoder->decode($newValue);

        $appSetting->setValue($decodedValue);

        $this->appSettingRepository->persist($appSetting);

        $this->unitOfWork->commit();
    }
}