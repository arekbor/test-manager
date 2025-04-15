<?php

declare(strict_types = 1);

namespace App\Application\Module\Repository;

use Doctrine\ORM\QueryBuilder;

interface ModuleRepositoryInterface
{
    public function getModuleViewModelsQueryBuilder(): QueryBuilder;
}