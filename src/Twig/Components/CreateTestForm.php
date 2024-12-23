<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Module;
use App\Form\CreateTestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
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

    #[LiveProp]
    public Module $moduleProp;
    
    #[LiveAction]
    public function submit(
        EntityManagerInterface $em,
    ): Response
    {
        $this->submitForm();

        $test = $this
            ->getForm()
            ->getData()
        ;

        $test->setModule($this->moduleProp);

        $em->persist($test);
        $em->flush();

        return $this->redirectToRoute('app_home_index');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CreateTestType::class);
    }
}
