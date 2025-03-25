<?php

declare(strict_types = 1);

namespace App\Application\Shared;

interface UnitOfWorkInterface
{
    public function commit(): void;
}