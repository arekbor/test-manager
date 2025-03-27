<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\CommandHandler;

use App\Application\AppSetting\Command\UpdateMailSmtpAppSetting;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\CryptoInterface;
use App\Domain\Model\MailSmtpAppSetting;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateMailSmtpAppSettingHandler
{
    public function __construct(
        private readonly CryptoInterface $crypto,
        private readonly AppSettingManagerInterface $appSettingManager
    ) {
    }

    public function __invoke(UpdateMailSmtpAppSetting $command): void
    {
        $mailSmtpAppSettingToUpdate = $command->getMailSmtpAppSetting();

        $plainPassword = $mailSmtpAppSettingToUpdate->getPassword();
        $encryptedPassword = $this->crypto->encrypt($plainPassword);
        $mailSmtpAppSettingToUpdate->setPassword($encryptedPassword);

        $this->appSettingManager->update(MailSmtpAppSetting::APP_SETTING_KEY, $mailSmtpAppSettingToUpdate);
    }
}