<?php

declare(strict_types=1);

namespace App\Util;

use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

final class AttributeHelper
{
    public static function getAttribute(ControllerArgumentsEvent $event, string $className): mixed
    {
        $attrs = $event->getAttributes();
        $attr = $attrs[$className] ?? null;

        return $attr;
    }
}