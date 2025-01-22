<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Builder\TestSolveBuilder;
use App\Entity\Test;
use App\Exception\NotFoundException;
use App\Form\TestSolveType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class TestSolveForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public Test $testProp;

    #[LiveAction]
    public function submit(): void
    {
        $this->submitForm();
        
        throw new NotImplementedException("Submit not implemented");
    }

    protected function instantiateForm(): FormInterface
    {
        $testCategory = $this->testProp->getModule()->getCategory() 
            ?? throw new NotFoundException(string::class, ['testCategory']);

        $testSolveBuilder = new TestSolveBuilder();
        $testSolve = $testSolveBuilder->build($this->testProp);

        return $this->createForm(TestSolveType::class, $testSolve, [
            'test_category' => $testCategory
        ]);
    }
}