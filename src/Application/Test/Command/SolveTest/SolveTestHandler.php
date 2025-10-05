<?php

declare(strict_types=1);

namespace App\Application\Test\Command\SolveTest;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Application\Test\Service\SendCsvTestResult;
use App\Application\Test\Service\TestResultCsvGenerator;
use App\Application\Test\Service\TestScoreCalculator;
use App\Domain\Entity\Test;
use App\Domain\Entity\TestResult;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\TestNotValidException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class SolveTestHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly TestScoreCalculator $testScoreCalculator,
        private readonly TestResultCsvGenerator $testResultCsvGenerator,
        private readonly SendCsvTestResult $sendCsvTestResult
    ) {}

    public function __invoke(SolveTest $command): void
    {
        if ($command->getStart() > $command->getSubmission()) {
            throw new \InvalidArgumentException('Start date cannot be later than submission date.');
        }

        $testId = $command->getTestId();

        /**
         * @var Test $test
         */
        $test = $this->entityManager->find(Test::class, $testId);
        if ($test === null) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }

        if (!$test->isValid()) {
            throw new TestNotValidException($test->getId());
        }

        $this->logger->info(sprintf(
            "[%s] Registering test solve for Test ID: %s",
            __CLASS__,
            $test->getId()->toString()
        ));

        $test->setStart($command->getStart());
        $test->setSubmission($command->getSubmission());

        $this->logger->info(sprintf(
            "[%s] Processing test solve for Test ID: %s.",
            __CLASS__,
            $test->getId()->toString(),
        ));

        $testSolve = $command->getTestSolve();

        $this->logger->info(sprintf(
            "[%s] Found %d test questions to process for Test ID: %s.",
            __CLASS__,
            $testSolve->getTestQuestionSolves()->count(),
            $test->getId()->toString()
        ));

        $score = $this->testScoreCalculator->calculate($testSolve, $test);

        $this->logger->info(sprintf(
            "[%s] Score successfully calculated for Test ID: %s.",
            __CLASS__,
            $test->getId()->toString()
        ));

        $test->setScore($score);
        $test->setFirstname($testSolve->getFirstname());
        $test->setLastname($testSolve->getLastname());
        $test->setEmail($testSolve->getEmail());
        $test->setWorkplace($testSolve->getWorkplace());
        $test->setDateOfBirth($testSolve->getDateOfBirth());

        $csv = $this->testResultCsvGenerator->create($test);

        $this->logger->info(sprintf(
            "[%s] Successfully generated CSV file for Test ID: %s",
            __CLASS__,
            $test->getId()->toString()
        ));

        $testResult = new TestResult();
        $testResult->setFile($csv);
        $test->setTestResult($testResult);
    }
}
