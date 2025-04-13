<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\QueryHandler;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Query\GetTestAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Domain\Exception\AppSettingByKeyNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetTestAppSettingHandler
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly AppSettingRepositoryInterface $appSettingRepository
    ) {
    }

    public function __invoke(GetTestAppSetting $query): TestAppSetting
    {
        $appSetting = $this->appSettingRepository->getByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new AppSettingByKeyNotFoundException(TestAppSetting::APP_SETTING_KEY);
        }

        return $this->appSettingManager->get($appSetting, TestAppSetting::class);
    }
}