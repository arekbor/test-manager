<?php

declare(strict_types = 1);

namespace App\Application\SecurityUser\Command;

use App\Application\SecurityUser\Model\UpdateEmail;
use Symfony\Component\Uid\Uuid;

final class UpdateSecurityUserEmail
{
    private Uuid $userId;
    private UpdateEmail $updateEmail;

    public function __construct(
        Uuid $userId,
        UpdateEmail $updateEmail
    ) {
        $this->userId = $userId;
        $this->updateEmail = $updateEmail;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getUpdateEmail(): UpdateEmail
    {
        return $this->updateEmail;
    }
}