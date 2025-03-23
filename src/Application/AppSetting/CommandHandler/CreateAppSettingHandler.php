<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\CommandHandler;

use App\Application\AppSetting\Command\CreateAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingDecoderInterface;
use App\Domain\Entity\AppSetting;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateAppSettingHandler
{
    public function __construct(
        private readonly AppSettingDecoderInterface $appSettingDecoder,
        private readonly AppSettingRepositoryInterface $appSettingRepository
    ) {
    }

    public function __invoke(CreateAppSetting $command): void
    {
        $value = $this->appSettingDecoder->decode($command->getValue());

        $appSetting = new AppSetting();

        $appSetting->setKey($command->getKeyAppSetting());
        $appSetting->setValue($value);

        $this->appSettingRepository->create($appSetting);
        $this->appSettingRepository->commitChanges();
    }
}