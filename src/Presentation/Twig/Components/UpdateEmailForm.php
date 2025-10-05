<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Form\FormInterface;
use App\Presentation\Form\UpdateEmailType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use App\Application\SecurityUser\Model\UpdateEmailModel;
use App\Domain\Exception\SecurityUserEmailUnchangedException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Application\SecurityUser\Command\UpdateEmail\UpdateEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsLiveComponent]
final class UpdateEmailForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

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

            $this->commandBus->handle(new UpdateEmail($userId, $updateEmailModel));
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
