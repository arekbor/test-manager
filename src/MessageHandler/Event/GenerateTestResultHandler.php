<?php

declare(strict_types = 1);

namespace App\MessageHandler\Event;

use App\Entity\Test;
use App\Exception\NotFoundException;
use App\Factory\TestResultFactory;
use App\Message\Event\GenerateTestResult;
use App\Repository\TestRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GenerateTestResultHandler
{
    public function __construct(
        private TestRepository $testRepository,
        private EntityManagerInterface $em,
        private EmailService $emailService
    ) {
    }

    public function __invoke(GenerateTestResult $event)
    {
        /**
         * @var Test
         */
        $test = $this->testRepository->find($event->getTestId());
        if (!$test) {
            throw new NotFoundException(Test::class, [$event->getTestId()]);
        }

        $testSolve = $event->getTestSolve();

        $test
            ->setScore($testSolve->calculateScore($test))
            ->setFirstname($testSolve->getFirstname())
            ->setLastname($testSolve->getLastname())
            ->setEmail($testSolve->getEmail())
            ->setWorkplace($testSolve->getWorkplace())
            ->setDateOfBirth($testSolve->getDateOfBirth())
        ;

        $testResult = (new TestResultFactory)->create($test);

        $file = $testResult->getFile();
        $recipient = $test->getCreator()->getEmail();
        $content = sprintf("Test result - %s %s", $test->getFirstname(), $test->getLastname());
        $this->emailService->sendEmail($recipient, $content, $content, $file);

        $this->em->persist($test);
        $this->em->persist($testResult);

        $this->em->flush();
    }
}