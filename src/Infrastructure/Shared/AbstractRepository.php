<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use App\Application\Shared\BaseRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository implements BaseRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function create(object $entity): void
    {
        $this->em->persist($entity);
    }

    public function commitChanges(): void
    {
        $this->em->flush();
    }
}