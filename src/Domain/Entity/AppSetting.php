<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class AppSetting extends BaseEntity
{
    #[ORM\Column(length: 255, unique: true)]
    private ?string $key = null;

    /**
     * @var array<mixed, mixed> $value
     */
    #[ORM\Column(type: Types::JSON)]
    private array $value = [];

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return array<mixed, mixed>
     */
    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @param array<mixed, mixed> $value
     */
    public function setValue(array $value): static
    {
        $this->value = $value;

        return $this;
    }
}
