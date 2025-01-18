<?php

namespace App\Twig\Runtime;

use App\Model\TestAppSetting;
use App\Model\TestMessageAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class TestMessageRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private AppSettingRepository $appSettingRepository,
        private AppSettingService $appSettingService,
        private RequestStack $requestStack,
        private TranslatorInterface $trans
    ) {
    }

    public function getIntroductionMessage(): string
    {
        $formatedMessage = $this->formatMessage(fn(TestMessageAppSetting $setting) => $setting->getIntroduction());

        return $formatedMessage ?? $this->trans->trans('twig.runtime.testMessage.introductionMessageNotFound');
    }

    public function getConclusionMessage(): string
    {
        $formatedMessage = $this->formatMessage(fn(TestMessageAppSetting $setting) => $setting->getConclusion());

        return $formatedMessage ?? $this->trans->trans('twig.runtime.testMessage.conclusionMessageNotFound');
    }

    private function formatMessage(callable $messageGetter): ?string 
    {
        $testMessageAppSetting = $this->getTestMessageAppSetting();
        $text = $testMessageAppSetting ? $messageGetter($testMessageAppSetting) : null;
        return $text ? nl2br(htmlspecialchars($text)) : null;
    }

    private function getTestMessageAppSetting(): ?TestMessageAppSetting 
    {
        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);

        $testMessages = $testAppSetting->getTestMessages();
        $language = $this->requestStack->getCurrentRequest()->getLocale();

        $filtered = array_filter($testMessages, function($testMessage) use($language) {
            return $testMessage->getLanguage() === $language;
        });

        $testMessageAppSetting = $filtered ? reset($filtered) : null; 

        return $testMessageAppSetting;
    }
}
