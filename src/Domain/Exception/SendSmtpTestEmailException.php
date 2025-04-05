<?php

declare(strict_types = 1);

namespace App\Domain\Exception;

final class SendSmtpTestEmailException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}