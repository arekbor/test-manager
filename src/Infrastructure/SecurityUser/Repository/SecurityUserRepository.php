<?php

declare(strict_types = 1);

namespace App\Infrastructure\SecurityUser\Repository;

use App\Application\SecurityUser\Repository\SecurityUserRepositoryInterface;
use App\Domain\Entity\SecurityUser;
use App\Infrastructure\Shared\AbstractRepository;

final class SecurityUserRepository extends AbstractRepository implements SecurityUserRepositoryInterface
{
    public function persistSecurityUser(SecurityUser $securityUser): void
    {
        $this->persist($securityUser);
    }
}