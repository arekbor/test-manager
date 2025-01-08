<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TestMessageAppSetting extends TestLanguage
{
    #[Assert\NotBlank]
    private ?string $introduction;
    #[Assert\NotBlank]
    private ?string $conclusion;

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
}