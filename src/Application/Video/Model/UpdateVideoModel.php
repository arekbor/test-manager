<?php 

declare(strict_types = 1);

namespace App\Application\Video\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateVideoModel 
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