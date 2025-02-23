<?php 

declare(strict_types=1);

namespace App\Service;

use App\Factory\MailerFactory;
use Symfony\Component\HttpFoundation\File\File;

class EmailService
{
    public function __construct(
        private MailerFactory $mailer
    ) {
    }

    public function sendEmail(string $recipient, string $subject, string $body, ?File $file = null): string
    {
        $mailer = $this->mailer->create();

        $mailer->addAddress($recipient);
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        if ($file) {
            $mailer->addAttachment($file->getPathname(), $file->getFilename());
        }

        $mailer->send();

        return $mailer->ErrorInfo;
    }
}