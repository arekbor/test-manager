<?php

declare(strict_types=1);

namespace App\Application\Module\Command\UpdateModule;

use App\Application\Module\Model\ModuleModel;
use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateModule implements CommandInterface
{
    public function __construct(
        private readonly Uuid $moduleId,
        private readonly ModuleModel $moduleModel
    ) {}

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getModuleModel(): ModuleModel
    {
        return $this->moduleModel;
    }
}
