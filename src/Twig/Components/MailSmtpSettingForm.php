<?php declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\MailSmtpSettingType;
use App\Model\MailSmtpSetting;
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
final class MailSmtpSettingForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private AppSettingService $appSettingService,
        private EncryptionService $encryptionService,
    ) {
    }

    #[LiveProp]
    public MailSmtpSetting $mailSmtpSetting;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $mailSmtpSetting = $this->getForm()->getData();

        $rawPassword = $mailSmtpSetting->getPassword();
        $encryptedPassword = $this->encryptionService->encrypt($rawPassword);
        $mailSmtpSetting->setPassword($encryptedPassword);

        $this->appSettingService->updateValue('mail.smtp', $mailSmtpSetting);

        return $this->redirectToRoute('app_home_index');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MailSmtpSettingType::class, $this->mailSmtpSetting);
    }
}