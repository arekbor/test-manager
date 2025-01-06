<?php

namespace App\Twig\Runtime;

use App\Model\TestAppSetting;
use App\Model\TestMessageAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use Twig\Extension\RuntimeExtensionInterface;

class TestMessageRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private AppSettingRepository $appSettingRepository,
        private AppSettingService $appSettingService
    ) {
    }

    public function getIntroductionMessage(string $language): ?string
    {
        $testMessageAppSetting = $this->getTestMessageAppSetting($language); 
        return $testMessageAppSetting ? $testMessageAppSetting->getIntroduction() : null;
    }

    public function getConclusionMessage(string $language): ?string
    {
        $testMessageAppSetting = $this->getTestMessageAppSetting($language); 
        return $testMessageAppSetting ? $testMessageAppSetting->getConclusion() : null;
    }

    private function getTestMessageAppSetting(string $language): ?TestMessageAppSetting 
    {
        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);

        $testMessages = $testAppSetting->getTestMessages();

        $filtered = array_filter($testMessages, function($testMessage) use($language) {
            return $testMessage->getLanguage() === $language;
        });

        $testMessageAppSetting = $filtered ? reset($filtered) : null; 

        return $testMessageAppSetting;
    }
}
