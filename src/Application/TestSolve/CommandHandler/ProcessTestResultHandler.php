<?php

declare(strict_types = 1);

namespace App\Application\TestSolve\CommandHandler;

use App\Application\Shared\UnitOfWorkInterface;
use App\Application\Test\Repository\TestRepositoryInterface;
use App\Application\TestResult\Repository\TestResultRepositoryInterface;
use App\Application\TestSolve\Command\ProcessTestResult;
use App\Application\TestSolve\Service\TestResultCsvGeneratorInterface;
use App\Application\TestSolve\Service\TestResultNotificationInterface;
use App\Application\TestSolve\Service\TestScoreCalculatorInterface;
use App\Domain\Entity\Test;
use App\Domain\Entity\TestResult;
use App\Domain\Model\TestSolve;
use App\Domain\Exception\NotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class ProcessTestResultHandler
{
    public function __construct(
        private readonly TestScoreCalculatorInterface $testScoreCalculator,
        private readonly TestResultCsvGeneratorInterface $testResultCsvGenerator,
        private readonly TestResultNotificationInterface $testResultNotification,
        private readonly TestRepositoryInterface $testRepository,
        private readonly TestResultRepositoryInterface $testResultRepository,
        private readonly UnitOfWorkInterface $unitOfWork,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(ProcessTestResult $command): void
    {
        $testId = $command->getTestId();

        $test = $this->testRepository->getTestById($testId);
        if (!$test) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }

        /**
         * @var TestSolve $testSolve
         */
        $testSolve = $command->getTestSolve();

        $score = $this->testScoreCalculator->calculate($testSolve, $test);

        $test->setScore($score);

        $testResultCsv = $this->testResultCsvGenerator->create($test);

        $testResult = new TestResult();
        $testResult->setTest($test);
        $testResult->setFile($testResultCsv);

        $this->testRepository->persistTest($test);
        $this->testResultRepository->persistTestResult($testResult);

        $this->unitOfWork->commit();

        try {
            $this->testResultNotification->send($test);
        } catch(\Exception $ex) {
            $this->logger->warning($ex->getMessage());
        }
    }
}