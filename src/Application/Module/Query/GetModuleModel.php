<?php

declare(strict_types = 1);

namespace App\Application\Module\Query;

use Symfony\Component\Uid\Uuid;

final class GetModuleModel
{
    private Uuid $moduleId;

    public function __construct(
        Uuid $moduleId
    ) {
        $this->moduleId = $moduleId;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }
}