<?php 

declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\SmtpTestType;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SmtpTestForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveAction]
    public function send(
        EmailService $emailService, 
        TranslatorInterface $trans
    ): Response
    {
        $this->submitForm();

        $testEmail = $this
            ->getForm()
            ->getData()
        ;

        $receiver = $testEmail->getReceiver();

        $error = $emailService->sendEmail($receiver, "Test Manager", "Test message")
        ;
        if (!empty($error)) {
            $this->addFlash('danger', $error);
            
            return $this->redirectToRoute('app_settings_smtptest');
        }

        $this->addFlash('success', $trans->trans('flash.testEmailForm.successEmailMessage'));

        return $this->redirectToRoute('app_settings_smtptest');
    } 

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SmtpTestType::class);
    }
}