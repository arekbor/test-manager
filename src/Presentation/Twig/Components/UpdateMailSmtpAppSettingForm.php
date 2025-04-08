<?php 

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\AppSetting\Command\UpdateMailSmtpAppSetting;
use App\Presentation\Form\MailSmtpAppSettingType;
use App\Application\AppSetting\Model\MailSmtpAppSetting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
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
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {
    }

    #[LiveProp]
    public MailSmtpAppSetting $mailSmtpAppSetting;

    #[LiveAction]
    public function submit(): Response
    {
        try {
            $this->submitForm();

            /**
             * @var MailSmtpAppSetting $mailSmtpAppSetting
             */
            $mailSmtpAppSetting = $this->getForm()->getData();

            $this->commandBus->dispatch(new UpdateMailSmtpAppSetting($mailSmtpAppSetting));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateMailSmtpAppSettingForm.error'));

            return $this->redirectToRoute('app_settings_smtp');
        }
        
        $this->addFlash('success', $this->trans->trans('flash.updateMailSmtpAppSettingForm.success'));
        
        return $this->redirectToRoute('app_settings_smtp');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MailSmtpAppSettingType::class, $this->mailSmtpAppSetting);
    }
}