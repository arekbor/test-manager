<?php

declare(strict_types=1);

namespace App\Application\Test\Service;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\EmailerInterface;
use App\Application\Shared\VichFileHandlerInterface;
use App\Domain\Entity\Test;
use App\Domain\Entity\TestResult;
use App\Domain\Exception\AppSettingByKeyNotFoundException;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final class SendCsvTestResult
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EmailerInterface $emailer,
        private readonly EntityManagerInterface $entityManager,
        private readonly VichFileHandlerInterface $vichFileHandler,
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly AppSettingRepositoryInterface $appSettingRepository
    ) {}

    public function send(Uuid $testId): void
    {
        /**
         * @var Test $test
         */
        $test = $this->entityManager->find(Test::class, $testId);
        if ($test === null) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }

        $appSetting = $this->appSettingRepository->getByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new AppSettingByKeyNotFoundException(TestAppSetting::APP_SETTING_KEY);
        }

        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingManager->get($appSetting, TestAppSetting::class);
        if (!$testAppSetting->getNotificationsEnabled()) {
            $this->logger->warning(sprintf(
                "[%s] Notifications for the test creator are disabled. Skipping notification sending.",
                __CLASS__
            ));

            return;
        }

        $recipient = $test->getCreator()->getEmail();
        $attachment = $this->vichFileHandler->handle($test->getTestResult(), TestResult::FILE_FIELD_NAME);

        $subject = sprintf("Test result - %s %s", $test->getFirstname(), $test->getLastname());
        $error = $this->emailer->send($recipient, $subject, "Test result", $attachment);

        if (!empty($error)) {
            $this->logger->warning(sprintf(
                "[%s] Failed to send test result email to %s. Error: %s",
                __CLASS__,
                $recipient,
                $error
            ));
        }
    }
}
