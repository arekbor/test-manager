<?php 

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test extends BaseEntity
{
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Module $module = null;

    #[ORM\Column(length: 255)]
    private ?string $takerEmail = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTimeInterface $expiration = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?\DateTimeInterface $submission = null;

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getTakerEmail(): ?string
    {
        return $this->takerEmail;
    }

    public function setTakerEmail(string $takerEmail): static
    {
        $this->takerEmail = $takerEmail;

        return $this;
    }

    public function getExpiration(): ?\DateTimeInterface
    {
        return $this->expiration;
    }

    public function setExpiration(\DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getSubmission(): ?\DateTimeInterface
    {
        return $this->submission;
    }

    public function setSubmission(\DateTimeInterface $submission): static
    {
        $this->submission = $submission;

        return $this;
    }
}