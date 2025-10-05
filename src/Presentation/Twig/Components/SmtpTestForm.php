<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\AppSetting\Command\SendSmtpTestEmail\SendSmtpTestEmail;
use App\Presentation\Form\SmtpTestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Application\AppSetting\Model\SmtpTest;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Domain\Exception\SendSmtpTestEmailException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsLiveComponent]
final class SmtpTestForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[LiveAction]
    public function send(): Response
    {
        $this->submitForm();

        $redirect = $this->redirectToRoute('app_settings_smtp');

        try {
            /**
             * @var SmtpTest $smtpTest
             */
            $smtpTest = $this->getForm()->getData();

            $this->commandBus->handle(new SendSmtpTestEmail($smtpTest));
        } catch (\Throwable $ex) {
            $errorMessage = $this->trans->trans('flash.testEmailForm.error');

            if ($ex instanceof HandlerFailedException && $ex->getPrevious() instanceof SendSmtpTestEmailException) {
                $errorMessage = $ex->getPrevious()->getMessage();
            }

            $this->addFlash('danger', $errorMessage);

            return $redirect;
        }

        $this->addFlash('success', $this->trans->trans('flash.testEmailForm.success'));

        return $redirect;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SmtpTestType::class);
    }
}
