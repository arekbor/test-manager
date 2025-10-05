<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use Symfony\Component\Uid\Uuid;
use App\Presentation\Form\TestType;
use App\Application\Test\Model\TestModel;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Test\Command\UpdateTest\UpdateTest;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
final class UpdateTestForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $testId;

    #[LiveProp(useSerializerForHydration: true)]
    public TestModel $testModel;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        try {
            /**
             * @var TestModel $testModel
             */
            $testModel = $this->getForm()->getData();

            $this->commandBus->handle(new UpdateTest($this->testId, $testModel));
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
