<?php declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TestEmail
{
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $receiver;

    public function getReceiver(): string
    {
        return $this->receiver;
    }

    public function setReceiver(string $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
}