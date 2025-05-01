<?php

declare(strict_types=1);

namespace App\Application\Util\QueryHandler;

use App\Application\Util\ComposerContentInterface;
use App\Application\Util\Query\GetTestManagerVersion;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetTestManagerVersionHandler
{
    public function __construct(private readonly ComposerContentInterface $composerContent) {}

    public function __invoke(GetTestManagerVersion $query): string
    {
        return $this->composerContent->getContent()['version'];
    }
}
