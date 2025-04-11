<?php

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\SecurityUser\Command\UpdateEmail;
use App\Presentation\Form\UpdateEmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Application\SecurityUser\Model\UpdateEmailModel;
use App\Domain\Exception\SecurityUserEmailUnchangedException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

#[AsLiveComponent]
final class UpdateEmailForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {
    }

    #[LiveAction]
    public function update(): Response
    {
        $this->submitForm();

        try {
            /**
             * @var UpdateEmailModel $updateEmailModel
             */
            $updateEmailModel = $this->getForm()->getData();

            $userId = Uuid::fromString($this->getUser()->getUserIdentifier());

            $this->commandBus->dispatch(new UpdateEmail($userId, $updateEmailModel));
        } catch (\Throwable $ex) {
            $errorMessage = $this->trans->trans('flash.updateEmailForm.error');

            if ($ex instanceof UniqueConstraintViolationException) {
                $errorMessage = $this->trans->trans('flash.updateEmailForm.emailAlreadyExists');
            }

            if ($ex instanceof HandlerFailedException && $ex->getPrevious() instanceof SecurityUserEmailUnchangedException) {
                $errorMessage = $this->trans->trans('flash.updateEmailForm.emailEnchanged');
            }

            $this->addFlash('danger', $errorMessage);

            return $this->redirectToRoute('app_settings_general');
        }

        return $this->redirectToRoute('app_auth_logout');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UpdateEmailType::class);
    }
}
