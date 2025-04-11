<?php 

declare(strict_types = 1);

namespace App\Application\SecurityUser\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateEmailModel
{
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}