<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\CommandHandler;

use App\Application\AppSetting\Command\CreateInitialAppSettings;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\RepositoryInterface;
use App\Domain\Model\MailSmtpAppSetting;
use App\Domain\Model\TestAppSetting;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateInitialAppSettingsHandler
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly RepositoryInterface $repository
    ) {
    }

    public function __invoke(CreateInitialAppSettings $command): void
    {
        $mailSmtpAppSetting = $this->appSettingManager->create(MailSmtpAppSetting::APP_SETTING_KEY, new MailSmtpAppSetting());
        $testAppSetting = $this->appSettingManager->create(TestAppSetting::APP_SETTING_KEY, new TestAppSetting());
        
        $this->repository->persist($mailSmtpAppSetting);
        $this->repository->persist($testAppSetting);
    }
}