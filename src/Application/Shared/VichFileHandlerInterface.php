<?php

declare(strict_types = 1);

namespace App\Application\Shared;

interface VichFileHandlerInterface
{
    public function handle(object|array $object, string $field, ?string $className = null): \SplFileInfo;
}