<?php

declare(strict_types = 1);

namespace App\Application\Module\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class ModuleModel
{
    #[Assert\NotBlank()]
    #[Assert\Length(max: 255)]
    private string $name;

    #[Assert\NotBlank()]
    #[Assert\Length(max: 5)]
    private string $language;

    #[Assert\NotBlank()]
    #[Assert\Length(max: 20)]
    private string $category;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }
}