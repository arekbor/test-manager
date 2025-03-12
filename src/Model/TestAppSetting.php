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

    private bool $notificationsEnabled;

    public function __construct() {
        $this->expirationDaysOffset = 7;
        $this->testMessages = [];
        $this->testClauses = [];
        $this->notificationsEnabled = true;
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

    public function setNotificationsEnabled(bool $notificationsEnabled): static
    {
        $this->notificationsEnabled = $notificationsEnabled;

        return $this;
    }

    public function getNotificationsEnabled(): bool
    {
        return $this->notificationsEnabled;
    }

    public function getTestMessageAppSettingByLanguage(string $language): ?TestMessageAppSetting
    {
        return ArrayHelper::findFirstByProperty($this->testMessages, 'getLanguage', $language);
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

    public function getTestClauseAppSettingByLanguage(string $language): ?TestClauseAppSetting
    {
        return ArrayHelper::findFirstByProperty($this->testClauses, 'getLanguage', $language);
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