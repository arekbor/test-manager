<?php

declare(strict_types = 1);

namespace App\Presentation\Twig\Extension;

use App\Presentation\Twig\Runtime\HtmlFormatRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class HtmlFormatExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('htmlFormat', [HtmlFormatRuntime::class, 'getHtmlFormatedText'], [
                'is_safe' => [
                    'html'
                ]
            ])
        ];
    }
}