<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Test\Command\RegisterTestSolve;
use App\Domain\Entity\Test;
use App\Domain\Event\TestSolveRegistered;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\TestNotValidException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RegisterTestSolveHandler
{
    public function __construct(
        private readonly MessageBusInterface $eventBus,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterTestSolve $command): void
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

        $this->logger->info(sprintf("[%s] Registering test solve for Test ID: %s", 
            __CLASS__,
            $test->getId()->toString()
        ));

        $test->setStart($command->getStart());
        $test->setSubmission($command->getSubmission());

        $this->eventBus->dispatch(new TestSolveRegistered($test->getId(), $command->getTestSolve()));
    }
}