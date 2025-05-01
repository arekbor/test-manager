<?php

declare(strict_types=1);

namespace App\Infrastructure\Util;

use App\Application\Util\ComposerContentInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class ComposerContent implements ComposerContentInterface
{
    public function __construct(
        private readonly ParameterBagInterface $params,
        private readonly KernelInterface $kernel
    ) {}

    public function getContent(): array
    {
        $filename = $this->kernel->getProjectDir() . '/composer.json';

        return json_decode(file_get_contents($filename), true);
    }
}
