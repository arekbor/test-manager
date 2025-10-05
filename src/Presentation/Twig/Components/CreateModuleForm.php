<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Presentation\Form\ModuleType;
use Symfony\Component\Form\FormInterface;
use App\Application\Module\Model\ModuleModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use App\Application\Module\Command\CreateModule\CreateModule;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
final class CreateModuleForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {}

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $redirect = $this->redirectToRoute('app_module_index');

        try {
            /**
             * @var ModuleModel $moduleModel
             */
            $moduleModel = $this->getForm()->getData();

            $this->commandBus->handle(new CreateModule($moduleModel));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.createModuleForm.error'));

            return $redirect;
        }

        $this->addFlash('success', $this->trans->trans('flash.createModuleForm.success'));

        return $redirect;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ModuleType::class);
    }
}
