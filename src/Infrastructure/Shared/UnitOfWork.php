<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use App\Application\Shared\UnitOfWorkInterface;
use Doctrine\ORM\EntityManagerInterface;

final class UnitOfWork implements UnitOfWorkInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function commit(): void
    {
        $this->em->flush();
    }
}