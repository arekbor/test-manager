<?php

declare(strict_types = 1);

namespace App\Application\Test\Repository;

use App\Domain\Entity\Test;
use Symfony\Component\Uid\Uuid;

interface TestRepositoryInterface
{
    public function getTestById(Uuid $id): ?Test;
    public function persistTest(Test $test): void;
}