<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Test\Command\RegisterTestSolve;
use App\Domain\Event\TestSolveRegistered;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RegisterTestSolveHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $eventBus,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterTestSolve $command): void
    {
        if ($command->getStart() > $command->getSubmission()) {
            throw new \InvalidArgumentException('Start date cannot be later than submission date.');
        }

        $test = $command->getTest();

        $testIdString = $test->getId()->toString();

        $this->logger->info(sprintf("[%s] Registering test solve for Test ID: %s", 
            __CLASS__,
            $testIdString
        ));

        $test->setStart($command->getStart());
        $test->setSubmission($command->getSubmission());

        $this->entityManager->persist($test);

        $this->eventBus->dispatch(new TestSolveRegistered($test->getId(), $command->getTestSolve()));
    }
}