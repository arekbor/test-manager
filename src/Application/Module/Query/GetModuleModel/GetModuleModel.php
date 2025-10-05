<?php

declare(strict_types=1);

namespace App\Application\Module\Query\GetModuleModel;

use App\Application\Shared\Bus\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class GetModuleModel implements QueryInterface
{
    public function __construct(
        private readonly Uuid $moduleId
    ) {}

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }
}
