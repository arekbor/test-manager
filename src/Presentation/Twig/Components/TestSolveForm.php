<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\Test\Command\RegisterTestSolve;
use App\Application\Test\Model\DataForTestSolve;
use App\Presentation\Form\TestSolveType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use App\Application\Test\Model\TestSolve;

#[AsLiveComponent]
final class TestSolveForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
    }

    #[LiveProp]
    public ?\DateTimeInterface $start = null;

    #[LiveProp(useSerializerForHydration: true)]
    public DataForTestSolve $dataForTestSolve;

    #[PreMount]
    public function preMount(): void
    {
        $this->start = new \DateTimeImmutable();
    }

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        try {
            /**
             * @var TestSolve $testSolve
             */
            $testSolve = $this->getForm()->getData();

            $this->commandBus->dispatch(new RegisterTestSolve(
                testId: $this->dataForTestSolve->getTestId(),
                testSolve: $testSolve,
                start: $this->start,
                submission: new \DateTimeImmutable()
            ));
        } catch (\Exception) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        return $this->redirectToRoute('app_testsolve_message', [
            'type' => 'conclusion',
            'id' => $this->dataForTestSolve->getTestId()
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestSolveType::class, $this->dataForTestSolve->getTestSolve(), [
            'test_category' => $this->dataForTestSolve->getTestCategory()
        ]);
    }
}