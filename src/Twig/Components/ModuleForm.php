<?php

namespace App\Twig\Components;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class ModuleForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?int $moduleId = null;

    public function __construct(
        private ModuleRepository $moduleRepository
    ) {
    }

    #[LiveAction]
    public function save(EntityManagerInterface $em): Response
    {
        $this->submitForm();
        $moduleForm = $this->getForm()->getData();

        $em->persist($moduleForm);
        $em->flush();

        return $this->redirectToRoute('app_home_index');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ModuleType::class, $this->getModule());
    }

    private function getModule(): ?Module
    {
        if ($this->moduleId) {
            return $this->moduleRepository->find($this->moduleId);
        }
        return null;
    }
}
