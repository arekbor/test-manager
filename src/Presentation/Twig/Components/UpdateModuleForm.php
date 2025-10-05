<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use Symfony\Component\Uid\Uuid;
use App\Presentation\Form\ModuleType;
use Symfony\Component\Form\FormInterface;
use App\Application\Module\Model\ModuleModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use App\Application\Module\Command\UpdateModule\UpdateModule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
final class UpdateModuleForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {}

    #[LiveProp]
    public ModuleModel $moduleModel;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

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

            $this->commandBus->handle(new UpdateModule($this->moduleId, $moduleModel));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateModuleForm.error'));

            return $redirect;
        }

        $this->addFlash('success', $this->trans->trans('flash.updateModuleForm.success'));

        return $redirect;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ModuleType::class, $this->moduleModel);
    }
}
