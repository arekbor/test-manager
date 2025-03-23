<?php

declare(strict_types = 1);

namespace App\Application\Shared;

interface BaseRepositoryInterface
{
    public function create(object $entity): void;
    public function commitChanges(): void;
}