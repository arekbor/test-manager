<?php

declare(strict_types=1);

namespace App\Application\AppSetting\Command\CreateInitialAppSettings;

use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\Shared\Bus\CommandBusHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CreateInitialAppSettingsHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(CreateInitialAppSettings $command): void
    {
        $mailSmtpAppSetting = $this->appSettingManager->create(MailSmtpAppSetting::APP_SETTING_KEY, new MailSmtpAppSetting());
        $testAppSetting = $this->appSettingManager->create(TestAppSetting::APP_SETTING_KEY, new TestAppSetting());

        $this->entityManager->persist($mailSmtpAppSetting);
        $this->entityManager->persist($testAppSetting);
    }
}
