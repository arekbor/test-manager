<?php 

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\Question\Command\AddQuestionToModule;
use App\Presentation\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use App\Application\Question\Model\QuestionModel;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsLiveComponent]
final class AddQuestionToModuleForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {
    }

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    #[LiveAction]
    public function submit(): Response
    {
        try {
            $this->submitForm();

            /**
             * @var QuestionModel $questionModel
             */
            $questionModel = $this->getForm()->getData();

            $this->commandBus->dispatch(new AddQuestionToModule($this->moduleId, $questionModel));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.addQuestionToModuleForm.error'));

            return $this->redirectToRoute('app_module_index');
        }

        $this->addFlash('success', $this->trans->trans('flash.addQuestionToModuleForm.success'));

        return $this->redirectToRoute('app_module_questions', [
            'id' => $this->moduleId
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(QuestionType::class);
    }
}
