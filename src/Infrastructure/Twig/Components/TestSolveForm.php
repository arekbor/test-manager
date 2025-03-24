<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig\Components;

use App\Application\Test\Command\UpdateTestWithTestSolve;
use App\Application\TestSolve\Command\ProcessTestResult;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
use App\Infrastructure\Form\TestSolveType;
use Doctrine\ORM\EntityManagerInterface;
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
use App\Domain\Model\TestSolve;

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
        private EntityManagerInterface $em,
        private MessageBusInterface $commandBus
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
        
        /**
         * @var TestSolve $testSolve
         */
        $testSolve = $this->getForm()->getData();

        try {
            $this->commandBus->dispatch(new UpdateTestWithTestSolve(
                test: $this->testProp,
                start: $this->start,
                submission: new \DateTimeImmutable(),
                testSolve: $testSolve
            ));
        } catch(\Exception) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        $this->commandBus->dispatch(new ProcessTestResult(
            testSolve: $testSolve,
            testId: $this->testProp->getId()
        ));

        return $this->redirectToRoute('app_testsolve_conclusion');
    }

    protected function instantiateForm(): FormInterface
    {
        $testCategory = $this->testProp->getModule()->getCategory() 
            ?? throw new NotFoundException(string::class, ['testCategory']);

        $testSolve = $this->testProp->toTestSolve();
        
        return $this->createForm(TestSolveType::class, $testSolve, [
            'test_category' => $testCategory
        ]);
    }
}