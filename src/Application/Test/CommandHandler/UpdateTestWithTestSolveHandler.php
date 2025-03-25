<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Shared\RepositoryInterface;
use App\Application\Shared\UnitOfWorkInterface;
use App\Application\Test\Command\UpdateTestWithTestSolve;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateTestWithTestSolveHandler
{
    public function __construct(
        private readonly RepositoryInterface $repository,
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

        $this->repository->persist($test);
        $this->unitOfWork->commit();
    }
}