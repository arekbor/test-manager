<?php

declare(strict_types = 1);

namespace App\MessageHandler\Event;

use App\Entity\AppSetting;
use App\Entity\Test;
use App\Entity\TestResult;
use App\Exception\NotFoundException;
use App\Message\Event\SubmitTestSolve;
use App\Model\TestAppSetting;
use App\Repository\AppSettingRepository;
use App\Repository\TestRepository;
use App\Service\AppSettingService;
use App\Service\EmailService;
use App\Service\TestService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
class SubmitTestSolveHandler
{
    public function __construct(
        private TestRepository $testRepository,
        private EntityManagerInterface $em,
        private EmailService $emailService,
        private AppSettingRepository $appSettingRepository,
        private AppSettingService $appSettingService,
        private TestService $testService,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(SubmitTestSolve $event)
    {
        /**
         * @var Test
         */
        $test = $this->testRepository->find($event->getTestId());
        if (!$test) {
            $this->logger->error("Test id: {$event->getTestId()} not found");

            throw new NotFoundException(Test::class, [$event->getTestId()]);
        }

        $testSolve = $event->getTestSolve();

        $test->setScore($testSolve->calculateScore($test));

        $csv = $this->testService->createCsv($test);

        $this->logger->info($csv->getFilename() . ' successfully created.');

        $testResult = new TestResult();

        $testResult->setTest($test);
        $testResult->setFile($csv);
        
        $this->em->persist($test);
        $this->em->persist($testResult);

        $this->em->flush();

        $this->sendNotification($test);
    }

    private function sendNotification(Test $test): void
    {
        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            $this->logger->error('App setting not found: ' . TestAppSetting::APP_SETTING_KEY);
            return;
        }

        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);

        if ($testAppSetting->getNotificationsEnabled()) {
            $file = $test->getTestResult()->getFile();
            
            $recipient = $test->getCreator()->getEmail();

            $content = sprintf("Test result - %s %s", $test->getFirstname(), $test->getLastname());

            $errorMessage = $this->emailService->send($recipient, $content, $content, $file);

            if ($errorMessage) {
                $this->logger->warning("Failed to send test result email to {$recipient} with attachemnt {$file->getFilename()}: " . $errorMessage);
            } else {
                $this->logger->info("Email with test result sent successfully to {$recipient} with attachemnt {$file->getFilename()}.");
            }
        }
    }
}