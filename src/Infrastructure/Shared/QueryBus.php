<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use App\Application\Shared\QueryBusInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function query(mixed $query): mixed
    {
        return $this->handle($query);
    }
}