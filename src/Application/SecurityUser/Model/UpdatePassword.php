<?php 

declare(strict_types = 1);

namespace App\Application\SecurityUser\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePassword
{
    #[Assert\PasswordStrength]
    #[Assert\NotBlank]
    private string $password;

    #[Assert\NotBlank]
    private string $currentPassword;

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(string $currentPassword): static
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }
}