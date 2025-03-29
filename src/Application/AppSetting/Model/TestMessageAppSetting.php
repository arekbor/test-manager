<?php

declare(strict_types = 1);

namespace App\Application\AppSetting\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TestMessageAppSetting
{
    #[Assert\NotBlank]
    private ?string $introduction;
    
    #[Assert\NotBlank]
    private ?string $conclusion;

    #[Assert\NotBlank]
    private ?string $language;

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): static 
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getConclusion(): ?string
    {
        return $this->conclusion;
    }

    public function setConclusion(?string $conclusion): static 
    {
        $this->conclusion = $conclusion;

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