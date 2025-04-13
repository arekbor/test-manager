<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\QueryHandler;

use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Application\AppSetting\Query\GetMailSmtpAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Domain\Exception\AppSettingByKeyNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetMailSmtpAppSettingHandler
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly AppSettingRepositoryInterface $appSettingRepository
    ) {
    }

    public function __invoke(GetMailSmtpAppSetting $query): MailSmtpAppSetting
    {
        $appSetting = $this->appSettingRepository->getByKey(MailSmtpAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new AppSettingByKeyNotFoundException(MailSmtpAppSetting::APP_SETTING_KEY);
        }

        return $this->appSettingManager->get($appSetting, MailSmtpAppSetting::class);
    }
}