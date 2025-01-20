<?php 

declare(strict_types=1);

namespace App\Service;

use App\Factory\MailerFactory;

class EmailService
{
    public function __construct(
        private MailerFactory $mailer
    ) {
    }

    public function sendEmail(string $recipient, string $subject, string $body): string
    {
        $mailer = $this->mailer->create();

        $mailer->addAddress($recipient);
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        $mailer->send();

        return $mailer->ErrorInfo;
    }
}