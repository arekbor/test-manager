<?php

declare(strict_types = 1);

namespace App\Application\Repository;

interface BaseRepositoryInterface
{
    public function create(object $entity): void;
    public function commitChanges(): void;
}