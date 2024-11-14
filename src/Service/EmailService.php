<?php declare(strict_types=1);

namespace App\Service;

use App\Model\MailSmtpAppSetting;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    public function __construct(
        private AppSettingService $appSettingService,
        private EncryptionService $encryptionService,
    ) {
    }

    public function sendEmail(string $recipient, string $subject, string $body): string
    {
        $mailer = $this->getPHPMailer();

        $mailer->addAddress($recipient);
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        $mailer->send();

        return $mailer->ErrorInfo;
    }

    private function getPHPMailer(): PHPMailer
    {
        $mailSmtpAppSetting = $this
            ->appSettingService
            ->getValue(MailSmtpAppSetting::APP_SETTING_KEY, MailSmtpAppSetting::class)
        ;

        $mailer = new PHPMailer();
        $mailer->isSMTP();
        $mailer->Host = $mailSmtpAppSetting->getHost();
        $mailer->Port = $mailSmtpAppSetting->getPort();
        $mailer->SMTPAuth = $mailSmtpAppSetting->getSmtpAuth();
        $mailer->Username = $mailSmtpAppSetting->getUsername();

        $encryptedPassword = $mailSmtpAppSetting->getPassword();
        $password = $this->encryptionService->decrypt($encryptedPassword);
        $mailer->Password = $password; 

        $mailer->SMTPSecure = $mailSmtpAppSetting->getSmtpSecure();
        $mailer->Timeout = $mailSmtpAppSetting->getTimeout();
        $mailer->setFrom($mailSmtpAppSetting->getFromAddress());
        
        return $mailer;
    }
}