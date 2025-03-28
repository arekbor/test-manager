<?php 

declare(strict_types=1);

namespace App\Service;

use App\Domain\Exception\NotFoundException;
use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Repository\AppSettingRepository;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    public function __construct(
        private AppSettingRepository $appSettingRepository,
        private AppSettingService $appSettingService,
        private EncryptionService $encryptionService
    ) {
    }

    public function send(string $recipient, string $subject, string $body, ?\SplFileInfo $file = null): string
    {
        $phpMailer = $this->getConfiguredPHPMailer();

        $phpMailer->addAddress($recipient);
        $phpMailer->Subject = $subject;
        $phpMailer->Body = $body;

        if ($file) {
            $phpMailer->addAttachment($file->getPathname(), $file->getFilename());
        }

        $phpMailer->send();

        return $phpMailer->ErrorInfo;
    }

    private function getConfiguredPHPMailer(): PHPMailer
    {
        $appSetting = $this
            ->appSettingRepository
            ->findOneByKey(MailSmtpAppSetting::APP_SETTING_KEY)
        ;

        if ($appSetting === null) {
            throw new NotFoundException(MailSmtpAppSetting::class);
        }

        $mailSmtpAppSetting = $this
            ->appSettingService
            ->getValue($appSetting, MailSmtpAppSetting::class)
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