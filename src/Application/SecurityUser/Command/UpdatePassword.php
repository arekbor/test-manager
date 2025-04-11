<?php

declare(strict_types = 1);

namespace App\Application\SecurityUser\Command;

use App\Application\SecurityUser\Model\UpdatePasswordModel;
use Symfony\Component\Uid\Uuid;

final class UpdatePassword
{
    private Uuid $userId;
    private UpdatePasswordModel $updatePasswordModel;

    public function __construct(
        Uuid $userId,
        UpdatePasswordModel $updatePasswordModel
    ) {
        $this->userId = $userId;
        $this->updatePasswordModel = $updatePasswordModel;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getUpdatePasswordModel(): UpdatePasswordModel
    {
        return $this->updatePasswordModel;
    }
}