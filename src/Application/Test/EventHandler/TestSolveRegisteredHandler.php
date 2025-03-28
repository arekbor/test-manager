<?php

declare(strict_types = 1);

namespace App\Application\Test\EventHandler;

use App\Application\Test\Command\ProcessTestSolve;
use App\Domain\Event\TestSolveRegistered;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'event.bus')]
final class TestSolveRegisteredHandler
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
    }

    public function __invoke(TestSolveRegistered $event): void
    {   
        $this->commandBus->dispatch(new ProcessTestSolve(
            testId: $event->getTestId(),
            testSolve: $event->getTestSolve()
        ));
    }
}