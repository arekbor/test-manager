<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Extension;

use App\Presentation\Twig\Runtime\TestManagerVersionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TestManagerVersionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('test_manager_version', [TestManagerVersionRuntime::class, 'getTestManagerVersion'])
        ];
    }
}
