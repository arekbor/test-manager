<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

abstract class TestLanguage
{
    #[Assert\NotBlank]
    private ?string $language;

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): static 
    {
        $this->language = $language;

        return $this;
    }
}