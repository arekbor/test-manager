<?php

declare(strict_types = 1);

namespace App\Tests;

use App\Domain\Exception\NotFoundException;
use Symfony\Component\Uid\Uuid;

final class EntityHelper
{
    public static function setId(Uuid $id, string $className, mixed $objectOrValue): void
    {
        $reflectionClass = new \ReflectionClass($className);
        $parentClass = $reflectionClass->getParentClass();
        if (!$parentClass) {
            throw new NotFoundException($className);
        }

        $property = $parentClass->getProperty('id');
        $property->setValue($objectOrValue, $id);
    }
}