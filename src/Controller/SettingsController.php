<?php

namespace App\Controller;

use App\Model\MailSmtpAppSetting;
use App\Model\TestAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    public function __construct(
        private AppSettingService $appSettingService,
        private AppSettingRepository $appSettingRepository) {
    }

    #[Route('/general')]
    public function general(): Response
    {
        return $this->render('settings/general.html.twig');
    }

    #[Route('/smtp')]
    public function smtp(
        
    ): Response
    {
        $appSetting = $this->appSettingRepository->findOneByKey(MailSmtpAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new NotFoundHttpException(MailSmtpAppSetting::class);
        }

        $mailSmtpAppSetting = $this->appSettingService->getValue($appSetting, MailSmtpAppSetting::class);

        return $this->render('settings/smtp.html.twig', [
            'mailSmtpAppSetting' => $mailSmtpAppSetting
        ]);
    }

    #[Route('/smtpTest')]
    public function smtpTest(): Response
    {
        return $this->render('settings/smtpTest.html.twig');
    }

    #[Route('/test')]
    public function test(): Response 
    {
        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new NotFoundHttpException(MailSmtpAppSetting::class);
        }

        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);

        return $this->render('settings/test.html.twig', [
            'testAppSetting' => $testAppSetting
        ]);
    }
}
