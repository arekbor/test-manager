<?php 

declare(strict_types=1);

namespace App\Presentation\Twig\Extension;

use App\Presentation\Twig\Runtime\LocaleRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocaleExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_locale_links', [LocaleRuntime::class, 'getLocaleLinks']),
        ];
    }
}
