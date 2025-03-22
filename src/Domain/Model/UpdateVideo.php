<?php 

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateVideo 
{
    #[Assert\NotBlank]
    private ?string $originalName;

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }
}