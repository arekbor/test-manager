<?php

declare(strict_types = 1);

namespace App\Application\TestSolve\CommandHandler;

use App\Application\Shared\RepositoryInterface;
use App\Application\Shared\UnitOfWorkInterface;
use App\Application\TestSolve\Command\ProcessTestResult;
use App\Application\TestSolve\Service\TestResultDocumentGenerator;
use App\Application\TestSolve\Service\TestResultNotification;
use App\Application\TestSolve\Service\TestScoreCalculator;
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
        private readonly TestScoreCalculator $testScoreCalculator,
        private readonly TestResultDocumentGenerator $testResultDocumentGenerator,
        private readonly TestResultNotification $testResultNotification,
        private readonly RepositoryInterface $repository,
        private readonly UnitOfWorkInterface $unitOfWork,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(ProcessTestResult $command): void
    {
        $testId = $command->getTestId();

        /**
         * @var Test $test
         */
        $test = $this->repository->get(Test::class, $testId);
        if (!$test) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }

        /**
         * @var TestSolve $testSolve
         */
        $testSolve = $command->getTestSolve();

        $score = $this->testScoreCalculator->calculate($testSolve, $test);

        $test->setScore($score);

        $testResultCsv = $this->testResultDocumentGenerator->create($test);

        $testResult = new TestResult();
        $testResult->setTest($test);
        $testResult->setFile($testResultCsv);

        $this->repository->persist($test);
        $this->repository->persist($testResult);

        $this->unitOfWork->commit();

        try {
            $this->testResultNotification->send($test);
        } catch(\Exception $ex) {
            $this->logger->warning($ex->getMessage());
        }
    }
}