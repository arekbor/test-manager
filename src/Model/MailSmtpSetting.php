<?php declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class MailSmtpSetting
{
    #[Assert\NotBlank]
    #[Assert\Hostname]
    private string $serverAddress;

    #[Assert\NotBlank]
    #[Assert\Length(max: 5)]
    #[Assert\Range(min: 1, max: 65535)]
    private string $serverPort;

    #[Assert\Email]
    #[Assert\NotBlank]
    private string $fromAddress;

    #[Assert\NotBlank]
    private string $name;

    #[Assert\NotBlank]
    private string $password;

    public function __construct() {
        $this->serverAddress = "";
        $this->serverPort = "";
        $this->fromAddress = "";
        $this->name = "";
        $this->password = "";
    }

    public function getServerAddress(): string
    {
        return $this->serverAddress;
    }

    public function setServerAddress(string $serverAddress): self
    {
        $this->serverAddress = $serverAddress;
        return $this;
    }

    public function getServerPort(): string
    {
        return $this->serverPort;
    }

    public function setServerPort(string $serverPort): self
    {
        $this->serverPort = $serverPort;
        return $this;
    }

    public function getFromAddress(): string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(string $fromAddress): self
    {
        $this->fromAddress = $fromAddress;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
}