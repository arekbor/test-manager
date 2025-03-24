<?php

declare(strict_types = 1);

namespace App\Application\Shared;

interface EmailerInterface
{
    public function send(string $recipient, string $subject, string $content, ?\SplFileInfo $attachment = null): string;
}