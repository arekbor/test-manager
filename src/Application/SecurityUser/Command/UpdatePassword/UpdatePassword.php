<?php

declare(strict_types=1);

namespace App\Application\SecurityUser\Command\UpdatePassword;

use App\Application\SecurityUser\Model\UpdatePasswordModel;
use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class UpdatePassword implements CommandInterface
{
    public function __construct(
        private readonly Uuid $userId,
        private readonly UpdatePasswordModel $updatePasswordModel
    ) {}

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getUpdatePasswordModel(): UpdatePasswordModel
    {
        return $this->updatePasswordModel;
    }
}
