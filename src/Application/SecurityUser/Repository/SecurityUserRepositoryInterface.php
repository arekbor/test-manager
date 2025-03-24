<?php

declare(strict_types = 1);

namespace App\Application\SecurityUser\Repository;

use App\Domain\Entity\SecurityUser;

interface SecurityUserRepositoryInterface
{
    public function persistSecurityUser(SecurityUser $securityUser): void;
}