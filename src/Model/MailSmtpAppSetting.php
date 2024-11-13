<?php declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class MailSmtpAppSetting
{
    public const APP_SETTING_KEY = "mail.smtp";

    #[Assert\NotBlank]
    #[Assert\Hostname]
    private string $host;

    #[Assert\NotBlank]
    #[Assert\Length(max: 5)]
    #[Assert\Range(min: 1, max: 65535)]
    private string $port;

    #[Assert\Email]
    #[Assert\NotBlank]
    private string $fromAddress;

    #[Assert\NotBlank]
    private string $username;

    #[Assert\NotBlank]
    private string $password;

    private bool $smtpAuth;

    #[Assert\NotBlank]
    private string $smtpSecure;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThanOrEqual(300)]
    private int $timeout;

    public function __construct() {
        $this->host = "";
        $this->port = "";
        $this->fromAddress = "";
        $this->username = "";
        $this->password = "";
        $this->smtpAuth = false;
        $this->smtpSecure = "";
        $this->timeout = 0;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function setPort(string $port): self
    {
        $this->port = $port;
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

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
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

    public function getSmtpAuth(): bool
    {
        return $this->smtpAuth;
    }

    public function setSmtpAuth(bool $smtpAuth): self
    {
        $this->smtpAuth = $smtpAuth;
        return $this;
    }

    public function getSmtpSecure(): string
    {
        return $this->smtpSecure;
    }

    public function setSmtpSecure(string $smtpSecure): self
    {
        $this->smtpSecure = $smtpSecure;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }
}