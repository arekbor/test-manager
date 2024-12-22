<?php

declare(strict_types=1);

namespace App\Model;

final class TestAppSetting
{
    public const APP_SETTING_KEY = "test";

    private string $welcomeMessage;

    private string $farewellMessage;

    public function __construct() {
        $this->welcomeMessage = "";
        $this->farewellMessage = "";
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
}