<?php

declare(strict_types=1);

namespace App\Application\Test\Query\GetTestPrivacyPolicyAppSetting;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestPrivacyPolicyAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\Bus\QueryBusHandlerInterface;

final class GetTestPrivacyPolicyAppSettingHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly AppSettingRepositoryInterface $appSettingRepository,
        private readonly AppSettingManagerInterface $appSettingManager
    ) {}

    public function __invoke(GetTestPrivacyPolicyAppSetting $query): ?TestPrivacyPolicyAppSetting
    {
        $appSetting = $this->appSettingRepository->getByKey(TestAppSetting::APP_SETTING_KEY);

        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingManager->get($appSetting, TestAppSetting::class);

        return $testAppSetting->getTestPrivacyPolicyAppSettingByLanguage($query->getLanguage());
    }
}
