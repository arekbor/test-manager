<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Test\Command\ProcessTestSolve;
use App\Application\Test\Service\TestResultCsvGenerator;
use App\Application\Test\Service\TestScoreCalculator;
use App\Domain\Entity\Test;
use App\Domain\Entity\TestResult;
use App\Domain\Event\TestSolveProcessed;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class ProcessTestSolveHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TestScoreCalculator $testScoreCalculator,
        private readonly TestResultCsvGenerator $testResultCsvGenerator,
        private readonly MessageBusInterface $eventBus,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(ProcessTestSolve $command): void
    {
        $testId = $command->getTestId();

        $testIdString = $testId->toString();

        $this->logger->info(sprintf("[%s] Processing test solve for Test ID: %s.",
            __CLASS__,
            $testIdString,
        ));

        $testSolve = $command->getTestSolve();

        $this->logger->info(sprintf("[%s] Found %d test questions to process for Test ID: %s.",
            __CLASS__,
            $testSolve->getTestQuestionSolves()->count(),
            $testIdString
        ));

        /**
         * @var Test $test
         */
        $test = $this->entityManager->find(Test::class, $testId);
        if ($test === null) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }

        $score = $this->testScoreCalculator->calculate($testSolve, $test);

        $this->logger->info(sprintf("[%s] Score successfully calculated for Test ID: %s.",
            __CLASS__,
            $testIdString
        ));

        $test->setScore($score);
        $test->setFirstname($testSolve->getFirstname());
        $test->setLastname($testSolve->getLastname());
        $test->setEmail($testSolve->getEmail());
        $test->setWorkplace($testSolve->getWorkplace());
        $test->setDateOfBirth($testSolve->getDateOfBirth());

        $csv = $this->testResultCsvGenerator->create($test);

        $this->logger->info(sprintf("[%s] Successfully generated CSV file for Test ID: %s", 
            __CLASS__,
            $test->getId()->toString()
        ));

        $testResult = new TestResult();
        $testResult->setFile($csv);

        $test->setTestResult($testResult);

        $this->eventBus->dispatch(new TestSolveProcessed($test->getId()));
    }
}