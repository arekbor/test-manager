<?php

declare(strict_types = 1);

namespace App\Application\Module\Command;

use App\Application\Module\Model\CreateModuleModel;

final class CreateModule
{
    private CreateModuleModel $createModule;

    public function __construct(CreateModuleModel $createModule) {
        $this->createModule = $createModule;
    }

    public function getCreateModule(): CreateModuleModel
    {
        return $this->createModule;
    }
}