<?php

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\Module\Command\UpdateModule;
use App\Application\Module\Model\ModuleModel;
use App\Presentation\Form\ModuleType;
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
final class UpdateModuleForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {
    }

    #[LiveProp]
    public ModuleModel $moduleModel;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        try {
            /**
             * @var ModuleModel $moduleModel
             */
            $moduleModel = $this->getForm()->getData();

            $this->commandBus->dispatch(new UpdateModule($this->moduleId, $moduleModel));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateModuleForm.error'));

            return $this->redirectToRoute('app_module_index');
        }

        $this->addFlash('success', $this->trans->trans('flash.updateModuleForm.success'));

        return $this->redirectToRoute('app_module_index');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ModuleType::class, $this->moduleModel);
    }
}