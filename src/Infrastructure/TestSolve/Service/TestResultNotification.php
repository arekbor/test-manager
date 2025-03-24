<?php

declare(strict_types = 1);

namespace App\Infrastructure\TestSolve\Service;

use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingDecoderInterface;
use App\Application\Shared\EmailerInterface;
use App\Application\TestSolve\Service\TestResultNotificationInterface;
use App\Domain\Entity\AppSetting;
use App\Domain\Entity\Test;
use App\Domain\Exception\EmailerException;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\TestResultNotificationDisabledException;
use App\Domain\Model\TestAppSetting;

final class TestResultNotification implements TestResultNotificationInterface
{
    public function __construct(
        private readonly AppSettingRepositoryInterface $appSettingRepository,
        private readonly AppSettingDecoderInterface $appSettingDecoder,
        private readonly EmailerInterface $emailer
    ) {
    }

    public function send(Test $test): void
    {
        $appSetting = $this->appSettingRepository->getByKey(TestAppSetting::APP_SETTING_KEY);
        if (!$appSetting) {
            throw new NotFoundException(AppSetting::class, ['key' => TestAppSetting::APP_SETTING_KEY]);
        }

        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingDecoder->encode($appSetting->getValue(), TestAppSetting::class);
        
        if ($testAppSetting->getNotificationsEnabled() === false) {
            throw new TestResultNotificationDisabledException();
        }

        $attachment = $test->getTestResult()->getFile();
        
        $recipient = $test->getCreator()->getEmail();

        $subject = sprintf("Test result - %s %s", $test->getFirstname(), $test->getLastname());

        $error = $this->emailer->send($recipient, $subject, "Test result", $attachment);
        if (!empty($error)) {
            throw new EmailerException($error);
        }
    }
}