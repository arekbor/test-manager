<?php

declare(strict_types=1);

namespace App\Application\Test\Query\GetTestModelWithDefaultExpirationDate;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\Bus\QueryBusHandlerInterface;
use App\Application\Test\Model\TestModel;
use App\Domain\Exception\AppSettingByKeyNotFoundException;
use App\Domain\Exception\DateTimeModifyException;

final class GetTestModelWithDefaultExpirationDateHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly AppSettingRepositoryInterface $appSettingRepository
    ) {}

    public function __invoke(GetTestModelWithDefaultExpirationDate $query): TestModel
    {
        $appSetting = $this->appSettingRepository->getByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new AppSettingByKeyNotFoundException(TestAppSetting::APP_SETTING_KEY);
        }

        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingManager->get($appSetting, TestAppSetting::class);

        $expirationDate = date_modify(new \DateTime(), sprintf('+%d days', $testAppSetting->getExpirationDaysOffset()));
        if ($expirationDate === false) {
            throw new DateTimeModifyException();
        }

        $testModel = new TestModel();
        $testModel->setExpiration($expirationDate);

        return $testModel;
    }
}
