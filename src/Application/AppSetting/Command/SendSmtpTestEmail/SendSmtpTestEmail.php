<?php

declare(strict_types=1);

namespace App\Application\AppSetting\Command\SendSmtpTestEmail;

use App\Application\AppSetting\Model\SmtpTest;
use App\Application\Shared\Bus\CommandInterface;

final class SendSmtpTestEmail implements CommandInterface
{
    public function __construct(private readonly SmtpTest $smtpTest) {}

    public function getSmtpTest(): SmtpTest
    {
        return $this->smtpTest;
    }
}
