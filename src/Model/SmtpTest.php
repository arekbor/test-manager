<?php 

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class SmtpTest
{
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $recipient;

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }
}