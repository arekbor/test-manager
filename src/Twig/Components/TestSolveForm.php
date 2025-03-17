<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Test;
use App\Exception\NotFoundException;
use App\Form\TestSolveType;
use App\Message\Event\SubmitTestSolve;
use DateTimeImmutable;
use DateTimeInterface;
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

#[AsLiveComponent]
final class TestSolveForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?DateTimeInterface $start = null;

    #[LiveProp]
    public Test $testProp;

    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $eventBus
    ) {
    }

    #[PreMount]
    public function preMount(): void
    {
        $this->start = new DateTimeImmutable();
    }

    #[LiveAction]
    public function submit(): Response
    {
        if (!$this->testProp->isValid()) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        $this->submitForm();
        
        $testSolve = $this->getForm()->getData();

        $this->testProp
            ->setStart($this->start)
            ->setSubmission(new DateTimeImmutable())
            ->setScore(null)
            ->setFirstname($testSolve->getFirstname())
            ->setLastname($testSolve->getLastname())
            ->setEmail($testSolve->getEmail())
            ->setWorkplace($testSolve->getWorkplace())
            ->setDateOfBirth($testSolve->getDateOfBirth())
        ;

        $this->em->persist($this->testProp);
        $this->em->flush();

        $this->eventBus->dispatch(new SubmitTestSolve($testSolve, $this->testProp->getId()));

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