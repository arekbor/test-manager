<?php

declare(strict_types = 1);

namespace App\Presentation\Controller;

use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\AppSetting\Query\GetMailSmtpAppSetting;
use App\Application\AppSetting\Query\GetTestAppSetting;
use App\Application\Shared\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {
    }

    #[Route('/general', name: 'app_settings_general')]
    public function general(): Response
    {
        return $this->render('settings/general.html.twig');
    }

    #[Route('/smtp', name: 'app_settings_smtp')]
    public function smtp(): Response
    {
        /**
         * @var MailSmtpAppSetting $mailSmtpAppSetting
         */
        $mailSmtpAppSetting = $this->queryBus->query(new GetMailSmtpAppSetting());

        return $this->render('settings/smtp.html.twig', [
            'mailSmtpAppSetting' => $mailSmtpAppSetting
        ]);
    }

    #[Route('/test', name: 'app_settings_test')]
    public function test(): Response 
    {
        /**
         * @var TestAppSetting $testAppSetting
         */
        $testAppSetting = $this->queryBus->query(new GetTestAppSetting());

        return $this->render('settings/test.html.twig', [
            'testAppSetting' => $testAppSetting
        ]);
    }
}
