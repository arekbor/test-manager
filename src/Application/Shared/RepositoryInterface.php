<?php

declare(strict_types = 1);

namespace App\Application\Shared;

interface RepositoryInterface
{
    public function persist(object $object): void;
    public function get(string $className, mixed $id): ?object;
}