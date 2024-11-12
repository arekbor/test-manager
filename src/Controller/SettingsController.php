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
    #[Route('/index')]
    public function index(
        AppSettingService $appSettingService
    ): Response
    {
        $mailSmtpAppSetting = $appSettingService
            ->getValue(MailSmtpAppSetting::APP_SETTING_KEY, MailSmtpAppSetting::class);

        return $this->render('settings/index.html.twig', [
            'mailSmtpAppSetting' => $mailSmtpAppSetting
        ]);
    }
}
