<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

final class TestAppSetting
{
    public const APP_SETTING_KEY = "test";

    #[Assert\GreaterThanOrEqual(0)]
    private int $expirationDaysOffset;

    #[Assert\Valid]
    #[AppAssert\UniqueLanguages]
    private array $testMessages;

    public function __construct() {
        $this->expirationDaysOffset = 7;
        $this->testMessages = [new TestMessageAppSetting()];
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

    public function getTestMessages(): array
    {
        return $this->testMessages;
    }

    public function addTestMessage(TestMessageAppSetting $testMessageAppSetting): static
    {
        if (!in_array($testMessageAppSetting, $this->testMessages, true)) {
            $this->testMessages[] = $testMessageAppSetting;
        }

        return $this;
    }

    public function removeTestMessage(TestMessageAppSetting $testMessageAppSetting): static
    {
        $key = array_search($testMessageAppSetting, $this->testMessages, true);
        if ($key !== false) {
            unset($this->testMessages[$key]);
            $this->testMessages = array_values($this->testMessages);
        }

        return $this;
    }
}