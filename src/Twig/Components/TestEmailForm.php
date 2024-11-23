<?php 

declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\TestEmailType;
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
final class TestEmailForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private EmailService $emailService,
        private TranslatorInterface $trans,
    ) {
    }

    #[LiveAction]
    public function send(): Response
    {
        $this->submitForm();

        $testEmail = $this->getForm()->getData();
        $receiver = $testEmail->getReceiver();

        $error = $this
            ->emailService
            ->sendEmail($receiver, "Test Manager", "Test message")
        ;
        if (!empty($error)) {
            $this->addFlash('danger', $error);
            return $this->redirectToRoute('app_settings_testmail');
        }

        $this->addFlash('success', $this->trans->trans('flash.testEmailForm.successEmailMessage'));

        return $this->redirectToRoute('app_settings_testmail');
    } 

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestEmailType::class);
    }
}