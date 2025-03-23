<?php

declare(strict_types = 1);

namespace App\Application\CommandHandler;

use App\Application\AppSetting\AppSettingDecoderInterface;
use App\Application\Command\CreateAppSetting;
use App\Application\Repository\AppSettingRepositoryInterface;
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