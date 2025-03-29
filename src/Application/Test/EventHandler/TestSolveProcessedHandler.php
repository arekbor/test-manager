<?php

declare(strict_types = 1);

namespace App\Application\Test\EventHandler;

use App\Application\Test\Command\SendTestResultCsvToTestCreator;
use App\Domain\Event\TestSolveProcessed;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'event.bus')]
final class TestSolveProcessedHandler
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
    }

    public function __invoke(TestSolveProcessed $event): void
    {
        $this->commandBus->dispatch(new SendTestResultCsvToTestCreator(
            testId: $event->getTestId()
        ));
    }
}