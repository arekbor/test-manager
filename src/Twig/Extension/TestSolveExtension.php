<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TestSolveRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TestSolveExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('introduction_message', [TestSolveRuntime::class, 'getIntroductionMessage'], [
                'is_safe' => [
                    'html'
                ]
            ]),
            new TwigFunction('conclusion_message', [TestSolveRuntime::class, 'getConclusionMessage'], [
                'is_safe' => [
                    'html'
                ]
            ]),
            new TwigFunction('clause', [TestSolveRuntime::class, 'getClause'], [
                'is_safe' => [
                    'html'
                ]
            ]),
        ];
    }
}
