<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\Shared\Bus\AsyncMessageInterface;
use App\Application\Shared\Bus\AsyncMessageBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class AsyncMessageBus implements AsyncMessageBusInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    public function send(AsyncMessageInterface $message): void
    {
        try {
            $this->messageBus->dispatch($message);
        } catch (HandlerFailedException $ex) {
            throw $ex->getPrevious() ?? throw new \Exception($ex->getMessage());
        }
    }
}
