<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

class TestAppSetting
{
    public const APP_SETTING_KEY = "test";

    #[Assert\GreaterThanOrEqual(0)]
    private int $expirationDaysOffset;

    #[Assert\Valid]
    #[AppAssert\UniqueLanguages]
    private array $testMessages;

    #[Assert\Valid]
    #[AppAssert\UniqueLanguages]
    private array $testClauses;

    public function __construct() {
        $this->expirationDaysOffset = 7;
        $this->testMessages = [];
        $this->testClauses = [];
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
        $this->addItem($this->testMessages, $testMessageAppSetting);

        return $this;
    }

    
    public function removeTestMessage(TestMessageAppSetting $testMessageAppSetting): static
    {
        $this->removeItem($this->testMessages, $testMessageAppSetting);

        return $this;
    }

    public function getTestClauses(): array
    {
        return $this->testClauses;
    }

    public function addTestClause(TestClauseAppSetting $testClauseAppSetting): static
    {
        $this->addItem($this->testClauses, $testClauseAppSetting);

        return $this;
    }

    public function removeTestClause(TestClauseAppSetting $testClauseAppSetting): static
    {
        $this->removeItem($this->testClauses, $testClauseAppSetting);

        return $this;
    }

    private function removeItem(array &$array, mixed $item): void
    {
        $key = array_search($item, $array, true);
        if ($key !== false) {
            unset($array[$key]);
            $array = array_values($array);
        }
    }

    private function addItem(array &$array, mixed $item): void
    {
        if (!in_array($item, $array, true)) {
            $array[] = $item;
        }
    }
}