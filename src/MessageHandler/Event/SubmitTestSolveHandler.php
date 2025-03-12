<?php

declare(strict_types = 1);

namespace App\MessageHandler\Event;

use App\Entity\AppSetting;
use App\Entity\Test;
use App\Exception\NotFoundException;
use App\Factory\TestResultFactory;
use App\Message\Event\SubmitTestSolve;
use App\Model\TestAppSetting;
use App\Repository\AppSettingRepository;
use App\Repository\TestRepository;
use App\Service\AppSettingService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
class SubmitTestSolveHandler
{
    public function __construct(
        private TestRepository $testRepository,
        private EntityManagerInterface $em,
        private EmailService $emailService,
        private AppSettingRepository $appSettingRepository,
        private AppSettingService $appSettingService
    ) {
    }

    public function __invoke(SubmitTestSolve $event)
    {
        /**
         * @var Test
         */
        $test = $this->testRepository->find($event->getTestId());
        if (!$test) {
            throw new NotFoundException(Test::class, [$event->getTestId()]);
        }

        $testSolve = $event->getTestSolve();

        $test->setScore($testSolve->calculateScore($test));

        $testResult = (new TestResultFactory)->create($test);

        $this->em->persist($test);
        $this->em->persist($testResult);

        $this->em->flush();

        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new NotFoundException(AppSetting::class);
        }

        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);

        if ($testAppSetting->getNotificationsEnabled()) {
            $this->sendEmail($test);
        }
    }

    private function sendEmail(Test $test): void
    {
        $file = $test->getTestResult()->getFile();
        $recipient = $test->getCreator()->getEmail();
        $content = sprintf("Test result - %s %s", $test->getFirstname(), $test->getLastname());
        $this->emailService->sendEmail($recipient, $content, $content, $file);
    }
}