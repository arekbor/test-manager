<?php

declare(strict_types=1);

namespace App\Application\Shared;

interface VichFileHandlerInterface
{
    public function handle(object $object, string $field, ?string $className = null): \SplFileInfo;
}
