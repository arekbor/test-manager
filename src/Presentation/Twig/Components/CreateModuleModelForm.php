<?php 

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\Module\Command\CreateModule;
use App\Application\Module\Model\CreateModuleModel;
use App\Presentation\Form\CreateModuleModelType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class CreateModuleModelForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {
    }

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();
        
        /**
         * @var CreateModuleModel $createModuleModel
         */
        $createModuleModel = $this->getForm()->getData();

        try {
            $this->commandBus->dispatch(new CreateModule($createModuleModel));
        } catch(\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.createModuleModelForm.error'));
    
            return $this->redirectToRoute('app_module_index');
        }

        $this->addFlash('success', $this->trans->trans('flash.createModuleModelForm.success'));

        return $this->redirectToRoute('app_module_index');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CreateModuleModelType::class);
    }
}
