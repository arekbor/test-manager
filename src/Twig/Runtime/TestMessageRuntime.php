<?php

namespace App\Twig\Runtime;

use App\Entity\Module;
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

    public function getWelcomeMessage(Module $module)
    {
        $testMessageAppSetting = $this->getTestMessageAppSetting($module); 
        return $testMessageAppSetting ? $testMessageAppSetting->getWelcome() : null;
    }

    public function getFarewellMessage(Module $module)
    {
        $testMessageAppSetting = $this->getTestMessageAppSetting($module); 
        return $testMessageAppSetting ? $testMessageAppSetting->getFarewell() : null;
    }

    private function getTestMessageAppSetting(Module $module): ?TestMessageAppSetting 
    {
        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);

        $testMessages = $testAppSetting->getTestMessages();
        $language = $module->getLanguage();

        $filtered = array_filter($testMessages, function($testMessage) use($language) {
            return $testMessage->getLanguage() === $language;
        });

        $testMessageAppSetting = $filtered ? reset($filtered) : null; 

        return $testMessageAppSetting;
    }
}
