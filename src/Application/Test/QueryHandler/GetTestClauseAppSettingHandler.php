<?php

declare(strict_types = 1);

namespace App\Application\Test\QueryHandler;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestClauseAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Test\Query\GetTestClauseAppSetting;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetTestClauseAppSettingHandler
{
    public function __construct(
        private readonly AppSettingRepositoryInterface $appSettingRepository,
        private readonly AppSettingManagerInterface $appSettingManager
    ) {
    }

    public function __invoke(GetTestClauseAppSetting $query): ?TestClauseAppSetting
    {
        $appSetting = $this->appSettingRepository->getByKey(TestAppSetting::APP_SETTING_KEY);

        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingManager->get($appSetting, TestAppSetting::class);

        return $testAppSetting->getTestClauseAppSettingByLanguage($query->getLanguage());
    }
}