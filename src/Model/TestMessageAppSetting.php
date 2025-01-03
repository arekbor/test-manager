<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class TestMessageAppSetting 
{
    #[Assert\NotBlank]
    private ?string $welcome;
    #[Assert\NotBlank]
    private ?string $farewell;
    #[Assert\NotBlank]
    private ?string $language;

    public function __construct() {
        $this->welcome = "";
        $this->farewell = "";
        $this->language = "";
    }

    public function getWelcome(): ?string
    {
        return $this->welcome;
    }

    public function setWelcome(?string $welcome): static 
    {
        $this->welcome = $welcome;

        return $this;
    }

    public function getFarewell(): ?string
    {
        return $this->farewell;
    }

    public function setFarewell(?string $farewell): static 
    {
        $this->farewell = $farewell;

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