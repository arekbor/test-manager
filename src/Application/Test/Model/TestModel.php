<?php

declare(strict_types = 1);

namespace App\Application\Test\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class TestModel
{
    #[Assert\GreaterThanOrEqual('now')]
    private \DateTimeInterface $expiration;

    public function setExpiration(\DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getExpiration(): \DateTimeInterface
    {
        return $this->expiration;
    }
}