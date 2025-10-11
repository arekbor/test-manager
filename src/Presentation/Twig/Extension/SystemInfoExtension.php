<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Presentation\Twig\Runtime\SystemInfoRuntime;

class SystemInfoExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_system_info', [SystemInfoRuntime::class, 'getSystemInfo'])
        ];
    }
}
