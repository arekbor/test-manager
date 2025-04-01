<?php

declare(strict_types = 1);

namespace App\Application\Module\Command;

use App\Application\Module\Model\ModuleModel;
use Symfony\Component\Uid\Uuid;

final class UpdateModule
{
    private Uuid $moduleId;
    private ModuleModel $moduleModel;

    public function __construct(Uuid $moduleId, ModuleModel $moduleModel) {
        $this->moduleId = $moduleId;
        $this->moduleModel = $moduleModel;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getModuleModel(): ModuleModel
    {
        return $this->moduleModel;
    }
}