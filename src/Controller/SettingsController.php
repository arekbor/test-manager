<?php

namespace App\Controller;

use App\Model\MailSmtpAppSetting;
use App\Service\AppSettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    #[Route('/general')]
    public function general(): Response
    {
        return $this->render('settings/general.html.twig');
    }

    #[Route('/smtp')]
    public function smtp(
        AppSettingService $appSettingService
    ): Response
    {
        $mailSmtpAppSetting = $appSettingService
            ->getValue(MailSmtpAppSetting::APP_SETTING_KEY, MailSmtpAppSetting::class);

        return $this->render('settings/smtp.html.twig', [
            'mailSmtpAppSetting' => $mailSmtpAppSetting
        ]);
    }

    #[Route('/testMail')]
    public function testMail(): Response
    {
        return $this->render('settings/testMail.html.twig');
    }
}
