<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TestPrivacyPolicyAppSetting
{
    #[Assert\NotBlank]
    private ?string $content;

    #[Assert\NotBlank]
    private ?string $language;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

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