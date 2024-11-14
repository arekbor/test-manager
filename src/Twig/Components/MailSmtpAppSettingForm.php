<?php declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\MailSmtpAppSettingType;
use App\Model\MailSmtpAppSetting;
use App\Service\AppSettingService;
use App\Service\EncryptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class MailSmtpAppSettingForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private AppSettingService $appSettingService,
        private EncryptionService $encryptionService,
    ) {
    }

    #[LiveProp]
    public MailSmtpAppSetting $mailSmtpAppSetting;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $mailSmtpAppSetting = $this->getForm()->getData();

        $plainPassword = $mailSmtpAppSetting->getPassword();
        $encryptedPassword = $this->encryptionService->encrypt($plainPassword);
        $mailSmtpAppSetting->setPassword($encryptedPassword);

        $this->appSettingService->updateValue(MailSmtpAppSetting::APP_SETTING_KEY, $mailSmtpAppSetting);

        return $this->redirectToRoute('app_settings_smtp');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MailSmtpAppSettingType::class, $this->mailSmtpAppSetting);
    }
}