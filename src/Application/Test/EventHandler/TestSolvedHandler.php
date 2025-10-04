<?php

declare(strict_types=1);

namespace App\Application\Test\EventHandler;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\EmailerInterface;
use App\Application\Shared\VichFileHandlerInterface;
use App\Domain\Entity\Test;
use App\Domain\Entity\TestResult;
use App\Domain\Event\TestSolved;
use App\Domain\Exception\AppSettingByKeyNotFoundException;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final class TestSolvedHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly AppSettingRepositoryInterface $appSettingRepository,
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly EmailerInterface $emailer,
        private readonly EntityManagerInterface $entityManager,
        private readonly VichFileHandlerInterface $vichFileHandler,
    ) {}

    public function __invoke(TestSolved $event): void
    {
        try {
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

            $testId = $event->getTestId();

            /**
             * @var Test $test
             */
            $test = $this->entityManager->find(Test::class, $testId);
            if ($test === null) {
                throw new NotFoundException(Test::class, ['id' => $testId]);
            }

            $recipient = $test->getCreator()->getEmail();
            $subject = sprintf("Test result - %s %s", $test->getFirstname(), $test->getLastname());
            $attachment = $this->vichFileHandler->handle($test->getTestResult(), TestResult::FILE_FIELD_NAME);
            $error = $this->emailer->send($recipient, $subject, "Test result", $attachment);

            if (!empty($error)) {
                $this->logger->warning(sprintf(
                    "[%s] Failed to send test result email to %s. Error: %s",
                    __CLASS__,
                    $recipient,
                    $error
                ));
            }
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
        }
    }
}
