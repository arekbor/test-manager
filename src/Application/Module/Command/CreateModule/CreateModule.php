<?php

declare(strict_types=1);

namespace App\Application\Module\Command\CreateModule;

use App\Application\Module\Model\ModuleModel;
use App\Application\Shared\Bus\CommandInterface;

final class CreateModule implements CommandInterface
{

    public function __construct(private readonly ModuleModel $moduleModel) {}

    public function getModuleModel(): ModuleModel
    {
        return $this->moduleModel;
    }
}
