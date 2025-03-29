<?php 

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\EmailerInterface;
use App\Application\Shared\RepositoryInterface;
use App\Application\Shared\VichFileHandlerInterface;
use App\Application\Test\Command\SendTestResultCsvToTestCreator;
use App\Domain\Entity\Test;
use App\Domain\Entity\TestResult;
use App\Domain\Exception\AppSettingByKeyNotFound;
use App\Domain\Exception\NotFoundException;
use App\Domain\Model\TestAppSetting;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class SendTestResultCsvToTestCreatorHandler
{
    public function __construct(
        private readonly AppSettingRepositoryInterface $appSettingRepository,
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly LoggerInterface $logger,
        private readonly EmailerInterface $emailer,
        private readonly RepositoryInterface $repository,
        private readonly VichFileHandlerInterface $vichFileHandler,
    ) {
    }

    public function __invoke(SendTestResultCsvToTestCreator $command): void
    {
        try {
            $appSetting = $this->appSettingRepository->getByKey(TestAppSetting::APP_SETTING_KEY);
            if (!$appSetting) {
                throw new AppSettingByKeyNotFound(TestAppSetting::APP_SETTING_KEY);
            }

            /**
             * @var TestAppSetting $testAppSetting
             */
            $testAppSetting = $this->appSettingManager->get($appSetting, TestAppSetting::class);

            if (!$testAppSetting->getNotificationsEnabled()) {
                $this->logger->warning(sprintf("[%s] Notifications for the test creator are disabled. Skipping notification sending.",
                    __CLASS__
                ));
    
                return;
            }

            $testId = $command->getTestId();

            /**
             * @var Test $test
             */
            $test = $this->repository->get(Test::class, $testId);
            if (!$test) {
                throw new NotFoundException(Test::class, ['id' => $testId]);
            }

            $recipient = $test->getCreator()->getEmail();

            $attachment = $this->vichFileHandler->handle($test->getTestResult(), TestResult::FILE_FIELD_NAME);

            $subject = sprintf("Test result - %s %s", $test->getFirstname(), $test->getLastname());

            $error = $this->emailer->send($recipient, $subject, "Test result", $attachment);

            if (!empty($error)) {
                $this->logger->warning(sprintf("[%s] Failed to send test result email to %s. Error: %s", 
                    __CLASS__, $recipient, $error
                ));
            }
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
        }
    }
}