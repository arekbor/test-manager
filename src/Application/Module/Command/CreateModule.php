<?php

declare(strict_types = 1);

namespace App\Application\Module\Command;

use App\Application\Module\Model\ModuleModel;

final class CreateModule
{
    private ModuleModel $moduleModel;

    public function __construct(ModuleModel $moduleModel) {
        $this->moduleModel = $moduleModel;
    }

    public function getModuleModel(): ModuleModel
    {
        return $this->moduleModel;
    }
}