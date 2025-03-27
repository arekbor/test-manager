<?php

declare(strict_types = 1);

namespace App\Application\Module\Command;

use App\Domain\Entity\Module;

final class CreateModule
{
    private Module $module;

    public function __construct(Module $module) {
        $this->module = $module;
    }

    public function getModule(): Module
    {
        return $this->module;
    }
}