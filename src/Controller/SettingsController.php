<?php

namespace App\Controller;

use App\Model\MailSmtpAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
class SettingsController extends BaseController
{
    #[Route('/general')]
    public function general(): Response
    {
        return $this->render('settings/general.html.twig');
    }

    #[Route('/smtp')]
    public function smtp(
        AppSettingService $appSettingService,
        AppSettingRepository $appSettingRepository
    ): Response
    {
        $appSetting = $appSettingRepository
            ->findByKey(MailSmtpAppSetting::APP_SETTING_KEY)
        ;

        if (empty($appSetting)) {
            throw new NotFoundHttpException(MailSmtpAppSetting::class);
        }

        $mailSmtpAppSetting = $appSettingService
            ->getValue($appSetting, MailSmtpAppSetting::class);

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
