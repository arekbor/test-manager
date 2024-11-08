<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\LocaleRuntime;
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
