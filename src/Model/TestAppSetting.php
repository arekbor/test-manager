<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class TestAppSetting
{
    public const APP_SETTING_KEY = "test";

    private string $welcomeMessage;

    private string $farewellMessage;

    #[Assert\GreaterThanOrEqual(0)]
    private int $expirationDaysOffset;

    public function __construct() {
        $this->welcomeMessage = "";
        $this->farewellMessage = "";
        $this->expirationDaysOffset = 7;
    }

    public function getWelcomeMessage(): string
    {
        return $this->welcomeMessage;
    }

    public function setWelcomeMessage(string $welcomeMessage): static
    {
        $this->welcomeMessage = $welcomeMessage;
        return $this;
    }

    public function getFarewellMessage(): string
    {
        return $this->farewellMessage;
    }

    public function setFarewellMessage(string $farewellMessage): static
    {
        $this->farewellMessage = $farewellMessage;
        return $this;
    }

    public function getExpirationDaysOffset(): int
    {
        return $this->expirationDaysOffset;
    }

    public function setExpirationDaysOffset(int $expirationDaysOffset): static
    {
        $this->expirationDaysOffset = $expirationDaysOffset;
        return $this;
    }
}