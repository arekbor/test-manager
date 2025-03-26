<?php

declare(strict_types = 1);

namespace App\Application\TestSolve\Service;

use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\EmailerInterface;
use App\Domain\Entity\Test;
use App\Domain\Exception\EmailerException;
use App\Domain\Exception\TestResultNotificationDisabledException;
use App\Domain\Model\TestAppSetting;

final class TestResultNotification
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly EmailerInterface $emailer
    ) {
    }

    public function send(Test $test): void
    {
        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingManager->get(TestAppSetting::APP_SETTING_KEY, TestAppSetting::class);
        
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