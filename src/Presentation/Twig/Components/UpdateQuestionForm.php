<?php

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\Question\Command\UpdateQuestion;
use App\Application\Question\Model\QuestionModel;
use App\Presentation\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class UpdateQuestionForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {
    }

    #[LiveProp(useSerializerForHydration: true)]
    public QuestionModel $questionModel;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $questionId;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        /**
         * @var QuestionModel $questionModel
         */
        $questionModel = $this->getForm()->getData();

        try {
            $this->commandBus->dispatch(new UpdateQuestion($this->questionId, $this->moduleId, $questionModel));
        } catch(\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateQuestionForm.error'));

            return $this->redirectToRoute('app_module_index');
        }

        $this->addFlash('success', $this->trans->trans('flash.updateQuestionForm.success'));

        return $this->redirectToRoute('app_module_questions', [
            'id' => $this->moduleId
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(QuestionType::class, $this->questionModel);
    }
}