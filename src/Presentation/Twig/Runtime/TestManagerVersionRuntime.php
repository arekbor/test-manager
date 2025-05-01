<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Runtime;

use App\Application\Shared\QueryBusInterface;
use App\Application\Util\Query\GetTestManagerVersion;
use Twig\Extension\RuntimeExtensionInterface;

final class TestManagerVersionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {}

    public function getTestManagerVersion(): string
    {
        return $this->queryBus->query(new GetTestManagerVersion());
    }
}
