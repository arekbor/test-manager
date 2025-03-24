<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Shared\UnitOfWorkInterface;
use App\Application\Test\Command\UpdateTestWithTestSolve;
use App\Application\Test\Repository\TestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateTestWithTestSolveHandler
{
    public function __construct(
        private readonly TestRepositoryInterface $testRepository,
        private readonly UnitOfWorkInterface $unitOfWork
    ) {
    }

    public function __invoke(UpdateTestWithTestSolve $command): void
    {
        $test = $command->getTest();

        $testSolve = $command->getTestSolve();

        $test->setStart($command->getStart());
        $test->setSubmission($command->getSubmission());
        $test->setScore(null);
        $test->setFirstname($testSolve->getFirstname());
        $test->setLastname($testSolve->getLastname());
        $test->setEmail($testSolve->getEmail());
        $test->setWorkplace($testSolve->getWorkplace());
        $test->setDateOfBirth($testSolve->getDateOfBirth());

        $this->testRepository->persistTest($test);

        $this->unitOfWork->commit();
    }
}