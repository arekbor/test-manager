<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use App\Application\Shared\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository implements RepositoryInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
    ) {
    }

    public function persist(object $object): void
    {
        $this->em->persist($object);
    }

    public function get(string $className, mixed $id): ?object
    {
        return $this->em->find($className, $id);
    }
}