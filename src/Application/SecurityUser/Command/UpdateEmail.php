<?php

declare(strict_types = 1);

namespace App\Application\SecurityUser\Command;

use App\Application\SecurityUser\Model\UpdateEmailModel;
use Symfony\Component\Uid\Uuid;

final class UpdateEmail
{
    private Uuid $userId;
    private UpdateEmailModel $updateEmailModel;

    public function __construct(
        Uuid $userId,
        UpdateEmailModel $updateEmailModel
    ) {
        $this->userId = $userId;
        $this->updateEmailModel = $updateEmailModel;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getUpdateEmailModel(): UpdateEmailModel
    {
        return $this->updateEmailModel;
    }
}