<?php

declare(strict_types=1);

namespace App\Application\AppSetting\Model;

use App\Application\Util\ArrayHelper;
use Symfony\Component\Validator\Constraints as Assert;
use App\Application\Validator as ApplicationAssert;

class TestAppSetting
{
    public const APP_SETTING_KEY = "test";

    #[Assert\GreaterThanOrEqual(1)]
    private int $expirationDaysOffset;

    /**
     * @var array<TestMessageAppSetting> $testMessageAppSettings
     */
    #[Assert\Valid]
    #[ApplicationAssert\UniqueValuesInArray(key: 'getLanguage')]
    private array $testMessageAppSettings;

    /**
     * @var TestPrivacyPolicyAppSetting[] $testPrivacyPolicyAppSettings
     */
    #[Assert\Valid]
    #[ApplicationAssert\UniqueValuesInArray(key: 'getLanguage')]
    private array $testPrivacyPolicyAppSettings;

    private bool $notificationsEnabled;

    public function __construct()
    {
        $this->expirationDaysOffset = 7;
        $this->testMessageAppSettings = [];
        $this->testPrivacyPolicyAppSettings = [];
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

    /**
     * @return array<TestMessageAppSetting>
     */
    public function getTestMessageAppSettings(): array
    {
        return $this->testMessageAppSettings;
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
        return ArrayHelper::findFirstByProperty($this->testMessageAppSettings, 'getLanguage', $language);
    }

    public function addTestMessageAppSetting(TestMessageAppSetting $testMessageAppSetting): static
    {
        ArrayHelper::addItem($this->testMessageAppSettings, $testMessageAppSetting);

        return $this;
    }

    public function removeTestMessageAppSetting(TestMessageAppSetting $testMessageAppSetting): static
    {
        ArrayHelper::removeItem($this->testMessageAppSettings, $testMessageAppSetting);

        return $this;
    }

    /**
     * @return TestPrivacyPolicyAppSetting[]
     */
    public function getTestPrivacyPolicyAppSettings(): array
    {
        return $this->testPrivacyPolicyAppSettings;
    }

    public function getTestPrivacyPolicyAppSettingByLanguage(string $language): ?TestPrivacyPolicyAppSetting
    {
        return ArrayHelper::findFirstByProperty($this->testPrivacyPolicyAppSettings, 'getLanguage', $language);
    }

    public function addTestPrivacyPolicyAppSetting(TestPrivacyPolicyAppSetting $testPrivacyPolicyAppSetting): static
    {
        ArrayHelper::addItem($this->testPrivacyPolicyAppSettings, $testPrivacyPolicyAppSetting);

        return $this;
    }

    public function removeTestPrivacyPolicyAppSetting(TestPrivacyPolicyAppSetting $testPrivacyPolicyAppSetting): static
    {
        ArrayHelper::removeItem($this->testPrivacyPolicyAppSettings, $testPrivacyPolicyAppSetting);

        return $this;
    }
}