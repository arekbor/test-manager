<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Command;

use App\Domain\Model\MailSmtpAppSetting;

final class UpdateMailSmtpAppSetting
{
    private MailSmtpAppSetting $mailSmtpAppSetting;

    public function __construct(
        MailSmtpAppSetting $mailSmtpAppSetting
    ) {
        $this->mailSmtpAppSetting = $mailSmtpAppSetting;
    }

    public function getMailSmtpAppSetting(): MailSmtpAppSetting
    {
        return $this->mailSmtpAppSetting;
    }
}