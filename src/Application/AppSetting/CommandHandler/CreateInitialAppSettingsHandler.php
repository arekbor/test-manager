<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\CommandHandler;

use App\Application\AppSetting\Command\CreateInitialAppSettings;
use App\Application\AppSetting\DTO\AppSettingToCreate;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Domain\Model\MailSmtpAppSetting;
use App\Domain\Model\TestAppSetting;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateInitialAppSettingsHandler
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
    ) {
    }

    public function __invoke(CreateInitialAppSettings $command): void
    {
        $appSettings = [
            new AppSettingToCreate(MailSmtpAppSetting::APP_SETTING_KEY, new MailSmtpAppSetting()),
            new AppSettingToCreate(TestAppSetting::APP_SETTING_KEY, new TestAppSetting())
        ];

        $this->appSettingManager->createMany(...$appSettings);
    }
}