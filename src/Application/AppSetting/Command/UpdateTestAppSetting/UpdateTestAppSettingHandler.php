<?php

declare(strict_types=1);

namespace App\Application\AppSetting\Command\UpdateTestAppSetting;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Exception\AppSettingByKeyNotFoundException;

final class UpdateTestAppSettingHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly AppSettingRepositoryInterface $appSettingRepository
    ) {}

    public function __invoke(UpdateTestAppSetting $command): void
    {
        $appSetting = $this->appSettingRepository->getByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new AppSettingByKeyNotFoundException(TestAppSetting::APP_SETTING_KEY);
        }

        $this->appSettingManager->update($appSetting, $command->getTestAppSetting());
    }
}
