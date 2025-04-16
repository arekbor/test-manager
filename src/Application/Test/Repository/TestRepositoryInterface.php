<?php

declare(strict_types = 1);

namespace App\Application\Test\Repository;

use Doctrine\ORM\QueryBuilder;

interface TestRepositoryInterface
{
    public function getTestViewModelsQueryBuilder(): QueryBuilder;
}