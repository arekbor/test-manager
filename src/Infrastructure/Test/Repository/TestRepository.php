<?php

declare(strict_types = 1);

namespace App\Infrastructure\Test\Repository;

use App\Application\Test\Repository\TestRepositoryInterface;
use App\Domain\Entity\Test;
use App\Infrastructure\Shared\AbstractRepository;
use Symfony\Component\Uid\Uuid;

final class TestRepository extends AbstractRepository implements TestRepositoryInterface 
{
    public function getTestById(Uuid $id): ?Test
    {
        return $this->get(Test::class, $id);
    }

    public function persistTest(Test $test): void
    {
        $this->persist($test);
    }
}