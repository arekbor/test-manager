<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Shared\RepositoryInterface;
use App\Application\Shared\UnitOfWorkInterface;
use App\Application\Test\Command\RegisterTestSolve;
use App\Domain\Event\TestSolveRegistered;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RegisterTestSolveHandler
{
    public function __construct(
        private readonly RepositoryInterface $repository,
        private readonly UnitOfWorkInterface $unitOfWork,
        private readonly MessageBusInterface $eventBus,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterTestSolve $command): void
    {
        if ($command->getStart() > $command->getSubmission()) {
            throw new \InvalidArgumentException('Start date cannot be later than submission date.');
        }

        try {
            $test = $command->getTest();

            $testIdString = $test->getId()->toString();

            $this->logger->info(sprintf("[%s] Registering test solve for Test ID: %s", 
                __CLASS__,
                $testIdString
            ));

            $test->setStart($command->getStart());
            $test->setSubmission($command->getSubmission());

            $this->repository->persist($test);
            $this->unitOfWork->commit();

            $this->eventBus->dispatch(new TestSolveRegistered(
                testId: $test->getId(),
                testSolve: $command->getTestSolve()
            ));

            $this->logger->info(sprintf("[%s] Successfully registered test solve for Test ID: %s", 
                __CLASS__,
                $testIdString
            ));

        } catch(\Exception $ex) {
            throw $ex;
        }
    }
}