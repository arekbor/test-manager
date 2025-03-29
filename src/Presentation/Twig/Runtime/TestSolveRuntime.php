<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Runtime;

use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Model\TestClauseAppSetting;
use App\Application\AppSetting\Model\TestMessageAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

class TestSolveRuntime implements RuntimeExtensionInterface
{
    private ?TestMessageAppSetting $testMessageAppSetting;
    private ?TestClauseAppSetting $testClauseAppSetting;

    public function __construct(
        private AppSettingRepository $appSettingRepository,
        private AppSettingService $appSettingService,
        private RequestStack $requestStack
    ) {
        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);

        $currentLanguage = $this->requestStack->getCurrentRequest()->getLocale();

        $this->testMessageAppSetting = $testAppSetting->getTestMessageAppSettingByLanguage($currentLanguage);
        $this->testClauseAppSetting = $testAppSetting->getTestClauseAppSettingByLanguage($currentLanguage);
    }

    public function getIntroductionMessage(): ?string
    {
        return $this->convertText($this->testMessageAppSetting?->getIntroduction());
    }

    public function getConclusionMessage(): ?string
    {
        return $this->convertText($this->testMessageAppSetting?->getConclusion());
    }

    public function getClause(): ?string
    {
        return $this->convertText($this->testClauseAppSetting?->getContent());
    }

    private function convertText(?string $text): ?string
    {
        return $text ? nl2br(htmlspecialchars($text)) : null;
    }
}
