<?php

declare(strict_types = 1);

namespace App\Domain\Exception;

final class SecurityUserEmailUnchangedException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf("Cannot update email: the provided email address is the same as the current one."));
    }
}