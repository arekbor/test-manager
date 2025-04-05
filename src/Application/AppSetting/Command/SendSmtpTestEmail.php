<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Command;

use App\Application\AppSetting\Model\SmtpTest;

final class SendSmtpTestEmail
{
    private SmtpTest $smtpTest;

    public function __construct(SmtpTest $smtpTest)
    {
        $this->smtpTest = $smtpTest;
    }

    public function getSmtpTest(): SmtpTest
    {
        return $this->smtpTest;
    }
}