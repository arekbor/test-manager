<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\CommandHandler;

use App\Application\AppSetting\Command\SendSmtpTestEmail;
use App\Application\Shared\EmailerInterface;
use App\Domain\Exception\SendSmtpTestEmailException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class SendSmtpTestEmailHandler
{
    public function __construct(
        private readonly EmailerInterface $emailer
    ) {
    }

    public function __invoke(SendSmtpTestEmail $command): void
    {
        $error = $this->emailer->send($command->getSmtpTest()->getRecipient(), "Test Manager", "Test message");
        if (!empty($error)) {
            throw new SendSmtpTestEmailException($error);
        }
    }
}