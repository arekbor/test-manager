<?php 

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TestRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test extends BaseEntity
{
    #[ORM\Column(length: 255)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $takerEmail = null;

    #[ORM\Column(type: 'default_datetime_tz', nullable: true)]
    #[Assert\GreaterThanOrEqual('now')]
    private ?\DateTimeInterface $expiration = null;

    #[ORM\Column(type: 'default_datetime_tz', nullable: true)]
    private ?\DateTimeInterface $submission = null;

    #[ORM\ManyToOne]
    private ?Module $module = null;

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

    public function setExpiration(?\DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getSubmission(): ?\DateTimeInterface
    {
        return $this->submission;
    }

    public function setSubmission(?\DateTimeInterface $submission): static
    {
        $this->submission = $submission;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function isValid(): bool
    {
        $now = new DateTime();

        return $now < $this->expiration && $this->submission === null;
    }
}