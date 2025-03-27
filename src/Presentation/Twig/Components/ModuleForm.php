<?php 

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\Module\Command\CreateModule;
use App\Domain\Entity\Module;
use App\Presentation\Form\ModuleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class ModuleForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {
    }

    #[LiveProp]
    public ?Module $moduleProp = null;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();
        
        $module = $this->getForm()->getData();

        try {
            $this->commandBus->dispatch(new CreateModule($module));
        } catch(\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.moduleForm.error'));
    
            return $this->redirectToRoute('app_module_index');
        }

        $this->addFlash('success', $this->trans->trans('flash.moduleForm.successfullyCreated', [
            '%moduleName%' => $module->getName()
        ]));

        return $this->redirectToRoute('app_module_index');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ModuleType::class, $this->moduleProp);
    }
}
