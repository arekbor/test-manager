<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\Test\Command\CreateTest;
use App\Presentation\Form\TestType;
use App\Application\Test\Model\TestModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class CreateTestForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    #[LiveProp(useSerializerForHydration: true)]
    public TestModel $testModel;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $redirect = $this->redirectToRoute('app_test_index');

        try {
            /**
             * @var TestModel $testModel
             */
            $testModel = $this->getForm()->getData();

            $creatorId = Uuid::fromString($this->getUser()->getUserIdentifier());

            $this->commandBus->dispatch(new CreateTest($testModel, $creatorId, $this->moduleId));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.createTestForm.error'));

            return $redirect;
        }

        $this->addFlash('success', $this->trans->trans('flash.createTestForm.success'));

        return $redirect;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestType::class, $this->testModel);
    }
}
