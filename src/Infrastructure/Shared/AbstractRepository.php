<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository
{
    public function __construct(
        protected readonly EntityManagerInterface $em
    ) {
    }

    public function get(string $className, mixed $id): ?object
    {
        return $this->em->find($className, $id);
    }

    public function persist(object $entity): void
    {
        $this->em->persist($entity);
    }
}