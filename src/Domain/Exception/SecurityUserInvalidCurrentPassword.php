<?php

declare(strict_types = 1);

namespace App\Domain\Exception;

final class SecurityUserInvalidCurrentPassword extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf("Invalid current password."));
    }
}