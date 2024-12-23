<?php 

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\TimezoneRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TimezoneExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_timezone', [TimezoneRuntime::class, 'getTimezone'])
        ];
    }
}