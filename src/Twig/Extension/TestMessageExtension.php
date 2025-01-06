<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TestMessageRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TestMessageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('introduction_message', [TestMessageRuntime::class, 'getIntroductionMessage']),
            new TwigFunction('conclusion_message', [TestMessageRuntime::class, 'getConclusionMessage']),
        ];
    }
}
