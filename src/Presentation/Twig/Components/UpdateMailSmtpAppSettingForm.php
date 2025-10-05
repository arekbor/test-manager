<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\AppSetting\Command\UpdateMailSmtpAppSetting\UpdateMailSmtpAppSetting;
use App\Presentation\Form\MailSmtpAppSettingType;
use App\Application\AppSetting\Model\MailSmtpAppSetting;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class UpdateMailSmtpAppSettingForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly TranslatorInterface $trans,
        private readonly CommandBusInterface $commandBus
    ) {}

    #[LiveProp]
    public MailSmtpAppSetting $mailSmtpAppSetting;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $redirect = $this->redirectToRoute('app_settings_smtp');

        try {
            /**
             * @var MailSmtpAppSetting $mailSmtpAppSetting
             */
            $mailSmtpAppSetting = $this->getForm()->getData();

            $this->commandBus->handle(new UpdateMailSmtpAppSetting($mailSmtpAppSetting));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateMailSmtpAppSettingForm.error'));

            return $redirect;
        }

        $this->addFlash('success', $this->trans->trans('flash.updateMailSmtpAppSettingForm.success'));

        return $redirect;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MailSmtpAppSettingType::class, $this->mailSmtpAppSetting);
    }
}
