<?php

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\Test\Command\UpdateTest;
use App\Application\Test\Model\TestModel;
use App\Presentation\Form\TestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class UpdateTestForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {
    }

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $testId;

    #[LiveProp(useSerializerForHydration: true)]
    public TestModel $testModel;

    #[LiveAction]
    public function submit(): Response
    {
        try {
            $this->submitForm();

            /**
             * @var TestModel $testModel
             */
            $testModel = $this->getForm()->getData();

            $this->commandBus->dispatch(new UpdateTest($this->testId, $testModel));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateTestForm.error'));

            return $this->redirectToRoute('app_test_index');
        }

        $this->addFlash('success', $this->trans->trans('flash.updateTestForm.success'));

        return $this->redirectToRoute('app_test_index');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestType::class, $this->testModel);
    }
}