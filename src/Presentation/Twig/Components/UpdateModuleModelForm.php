<?php

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\Module\Command\UpdateModule;
use App\Application\Module\Model\UpdateModuleModel;
use App\Presentation\Form\UpdateModuleModelType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class UpdateModuleModelForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {
    }

    #[LiveProp]
    public UpdateModuleModel $updateModuleModel;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        /**
         * @var UpdateModuleModel $updateModuleModel
         */
        $updateModuleModel = $this->getForm()->getData();

        try {
            $this->commandBus->dispatch(new UpdateModule($this->moduleId, $updateModuleModel));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateModuleModelForm.error'));

            return $this->redirectToRoute('app_module_index');
        }

        $this->addFlash('success', $this->trans->trans('flash.updateModuleModelForm.success'));

        return $this->redirectToRoute('app_module_index');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UpdateModuleModelType::class, $this->updateModuleModel);
    }
}