<?php 

declare(strict_types = 1);

namespace App\Application\Shared;

interface QueryBusInterface
{
    public function query(mixed $query): mixed;
}