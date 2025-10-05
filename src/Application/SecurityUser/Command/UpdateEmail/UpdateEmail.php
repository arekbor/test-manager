<?php

declare(strict_types=1);

namespace App\Application\SecurityUser\Command\UpdateEmail;

use App\Application\SecurityUser\Model\UpdateEmailModel;
use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateEmail implements CommandInterface
{
    public function __construct(
        private readonly Uuid $userId,
        private readonly UpdateEmailModel $updateEmailModel
    ) {}

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getUpdateEmailModel(): UpdateEmailModel
    {
        return $this->updateEmailModel;
    }
}
