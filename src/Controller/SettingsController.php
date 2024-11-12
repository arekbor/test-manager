<?php

namespace App\Controller;

use App\Model\MailSmtpSetting;
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
        $mailSmtpSetting = $appSettingService
            ->getValue('mail.smtp', MailSmtpSetting::class);

        return $this->render('settings/index.html.twig', [
            'mailSmtpSetting' => $mailSmtpSetting
        ]);
    }
}
