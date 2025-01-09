<?php

declare(strict_types=1);

namespace App\Model;

use App\Util\ArrayHelper;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

class TestAppSetting
{
    public const APP_SETTING_KEY = "test";

    #[Assert\GreaterThanOrEqual(0)]
    private int $expirationDaysOffset;

    #[Assert\Valid]
    #[AppAssert\UniqueValuesInArray(key: 'getLanguage')]
    private array $testMessages;

    #[Assert\Valid]
    #[AppAssert\UniqueValuesInArray(key: 'getLanguage')]
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
        ArrayHelper::addItem($this->testMessages, $testMessageAppSetting);

        return $this;
    }

    
    public function removeTestMessage(TestMessageAppSetting $testMessageAppSetting): static
    {
        ArrayHelper::removeItem($this->testMessages, $testMessageAppSetting);

        return $this;
    }

    public function getTestClauses(): array
    {
        return $this->testClauses;
    }

    public function addTestClause(TestClauseAppSetting $testClauseAppSetting): static
    {
        ArrayHelper::addItem($this->testClauses, $testClauseAppSetting);

        return $this;
    }

    public function removeTestClause(TestClauseAppSetting $testClauseAppSetting): static
    {
        ArrayHelper::removeItem($this->testClauses, $testClauseAppSetting);

        return $this;
    }
}