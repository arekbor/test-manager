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
            new TwigFunction('welcome_message', [TestMessageRuntime::class, 'getWelcomeMessage']),
            new TwigFunction('farewell_message', [TestMessageRuntime::class, 'getFarewellMessage']),
        ];
    }
}
