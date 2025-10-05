<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use Symfony\Component\Uid\Uuid;
use App\Presentation\Form\QuestionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Application\Question\Model\QuestionModel;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\Question\Command\AddQuestionToModule\AddQuestionToModule;

#[AsLiveComponent]
final class AddQuestionToModuleForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        try {
            /**
             * @var QuestionModel $questionModel
             */
            $questionModel = $this->getForm()->getData();

            $this->commandBus->handle(new AddQuestionToModule($this->moduleId, $questionModel));
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
