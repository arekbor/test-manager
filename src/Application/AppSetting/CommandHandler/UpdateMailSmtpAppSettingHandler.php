<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\CommandHandler;

use App\Application\AppSetting\Command\UpdateMailSmtpAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\CryptoInterface;
use App\Domain\Exception\AppSettingByKeyNotFound;
use App\Application\AppSetting\Model\MailSmtpAppSetting;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateMailSmtpAppSettingHandler
{
    public function __construct(
        private readonly CryptoInterface $crypto,
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly AppSettingRepositoryInterface $appSettingRepository
    ) {
    }

    public function __invoke(UpdateMailSmtpAppSetting $command): void
    {
        $mailSmtpAppSettingToUpdate = $command->getMailSmtpAppSetting();

        $plainPassword = $mailSmtpAppSettingToUpdate->getPassword();
        $encryptedPassword = $this->crypto->encrypt($plainPassword);
        $mailSmtpAppSettingToUpdate->setPassword($encryptedPassword);

        $appSetting = $this->appSettingRepository->getByKey(MailSmtpAppSetting::APP_SETTING_KEY);
        if (!$appSetting) {
            throw new AppSettingByKeyNotFound(MailSmtpAppSetting::APP_SETTING_KEY);
        }

        $updatedAppSetting = $this->appSettingManager->update($appSetting, $mailSmtpAppSettingToUpdate);

        $this->appSettingRepository->persist($updatedAppSetting);
    }
}