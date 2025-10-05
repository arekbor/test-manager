<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Form\FormInterface;
use App\Presentation\Form\UpdatePasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use App\Application\SecurityUser\Model\UpdatePasswordModel;
use App\Domain\Exception\SecurityUserInvalidCurrentPassword;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use App\Application\SecurityUser\Command\UpdatePassword\UpdatePassword;

#[AsLiveComponent]
final class UpdatePasswordForm extends AbstractController
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
             * @var UpdatePasswordModel $updatePasswordModel
             */
            $updatePasswordModel = $this->getForm()->getData();

            $userId = Uuid::fromString($this->getUser()->getUserIdentifier());

            $this->commandBus->handle(new UpdatePassword($userId, $updatePasswordModel));
        } catch (\Throwable $ex) {
            $errorMessage = $this->trans->trans('flash.updatePasswordForm.error');

            if ($ex instanceof HandlerFailedException && $ex->getPrevious() instanceof SecurityUserInvalidCurrentPassword) {
                $errorMessage = $this->trans->trans('flash.updatePasswordForm.invalidPassword');
            }

            $this->addFlash('danger', $errorMessage);

            return $this->redirectToRoute('app_settings_general');
        }

        return $this->redirectToRoute('app_auth_logout');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UpdatePasswordType::class);
    }
}
