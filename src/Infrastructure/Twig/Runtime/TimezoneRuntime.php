<?php 

declare(strict_types=1);

namespace App\Infrastructure\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class TimezoneRuntime implements RuntimeExtensionInterface
{
    public function getTimezone(): string
    {
        return date_default_timezone_get();
    }
}