<?php 

declare(strict_types=1);

namespace App\Presentation\Twig\Extension;

use App\Presentation\Twig\Runtime\TimezoneRuntime;
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