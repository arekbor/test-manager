<?php

declare(strict_types = 1);

namespace App\Infrastructure\Persistence;

use App\Application\Repository\BaseRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class BaseRepository implements BaseRepositoryInterface
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