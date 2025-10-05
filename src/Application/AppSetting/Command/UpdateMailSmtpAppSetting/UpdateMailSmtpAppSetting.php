<?php

declare(strict_types=1);

namespace App\Application\AppSetting\Command\UpdateMailSmtpAppSetting;

use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Application\Shared\Bus\CommandInterface;

final class UpdateMailSmtpAppSetting implements CommandInterface
{
    public function __construct(
        private readonly MailSmtpAppSetting $mailSmtpAppSetting
    ) {}

    public function getMailSmtpAppSetting(): MailSmtpAppSetting
    {
        return $this->mailSmtpAppSetting;
    }
}
