<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\Test\Command\RegisterTestSolve;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
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
use App\Application\Test\Service\TestSolveFactory;

#[AsLiveComponent]
final class TestSolveForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?\DateTimeInterface $start = null;

    #[LiveProp]
    public Test $testProp;

    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
    }

    #[PreMount]
    public function preMount(): void
    {
        $this->start = new \DateTimeImmutable();
    }

    #[LiveAction]
    public function submit(): Response
    {
        if (!$this->testProp->isValid()) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        $this->submitForm();

        try {
            /**
             * @var TestSolve $testSolve
             */
            $testSolve = $this->getForm()->getData();

            $this->commandBus->dispatch(new RegisterTestSolve(
                test: $this->testProp,
                testSolve: $testSolve,
                start: $this->start,
                submission: new \DateTimeImmutable()
            ));
        } catch (\Exception) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        return $this->redirectToRoute('app_testsolve_conclusion');
    }

    protected function instantiateForm(): FormInterface
    {
        $testCategory = $this->testProp->getModule()->getCategory() 
            ?? throw new NotFoundException(string::class, ['testCategory']);

        $testSolve = TestSolveFactory::createFromModule($this->testProp->getModule());
        
        return $this->createForm(TestSolveType::class, $testSolve, [
            'test_category' => $testCategory
        ]);
    }
}