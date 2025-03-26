<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use App\Application\AppSetting\Service\AppSettingManagerInterface;
use App\Application\Shared\CryptoInterface;
use App\Application\Shared\EmailerInterface;
use App\Domain\Model\MailSmtpAppSetting;
use PHPMailer\PHPMailer\PHPMailer;

final class Emailer implements EmailerInterface
{
    public function __construct(
        private readonly AppSettingManagerInterface $appSettingManager,
        private readonly CryptoInterface $crypto
    ) {
    }

    public function send(string $recipient, string $subject, string $content, ?\SplFileInfo $attachment = null): string
    {
        $mailer = $this->getPHPMailer();

        $mailer->addAddress($recipient);
        $mailer->Subject = $subject;
        $mailer->Body = $content;

        if ($attachment) {
            $mailer->addAttachment($attachment->getPathname(), $attachment->getFilename());
        }

        $mailer->send();

        return $mailer->ErrorInfo;
    }

    private function getPHPMailer(): PHPMailer
    {
        /**
         * @var MailSmtpAppSetting $mailSmtpAppSetting
         */
        $mailSmtpAppSetting = $this->appSettingManager->get(MailSmtpAppSetting::APP_SETTING_KEY, MailSmtpAppSetting::class);

        $mailer = new PHPMailer();

        $mailer->isSMTP();
        $mailer->Host = $mailSmtpAppSetting->getHost();
        $mailer->Port = $mailSmtpAppSetting->getPort();
        $mailer->SMTPAuth = $mailSmtpAppSetting->getSmtpAuth();
        $mailer->Username = $mailSmtpAppSetting->getUsername();
        $mailer->SMTPSecure = $mailSmtpAppSetting->getSmtpSecure();
        $mailer->Timeout = $mailSmtpAppSetting->getTimeout();
        $mailer->setFrom($mailSmtpAppSetting->getFromAddress());

        $encryptedPassword = $mailSmtpAppSetting->getPassword();
        $password = $this->crypto->decrypt($encryptedPassword);
        $mailer->Password = $password;

        return $mailer;
    }
}