<?php

declare(strict_types = 1);

namespace App\Application\Video\Repository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

interface VideoRepositoryInterface
{
    public function getVideoViewModelsQueryBuilder(Uuid $moduleId): QueryBuilder;
}