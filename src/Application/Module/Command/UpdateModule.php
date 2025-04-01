<?php

declare(strict_types = 1);

namespace App\Application\Module\Command;

use App\Application\Module\Model\UpdateModuleModel;
use Symfony\Component\Uid\Uuid;

final class UpdateModule
{
    private Uuid $moduleId;
    private UpdateModuleModel $updateModuleModel;

    public function __construct(Uuid $moduleId, UpdateModuleModel $updateModuleModel) {
        $this->moduleId = $moduleId;
        $this->updateModuleModel = $updateModuleModel;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getUpdateModuleModel(): UpdateModuleModel
    {
        return $this->updateModuleModel;
    }
}