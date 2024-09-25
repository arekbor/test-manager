<?php

namespace App\Twig\Components;

use App\Entity\Module;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\ModuleRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
final class QuestionForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public int $moduleId;

    #[LiveProp]
    public ?int $questionId = null;

    public function __construct(
        private QuestionRepository $questionRepository, 
        private ModuleRepository $moduleRepository
    ) {
    }

    #[LiveAction]
    public function save(EntityManagerInterface $em): Response
    {
        $this->submitForm();
        $questionForm = $this->getForm()->getData();
        $module = $this->getModule();

        if ($module) {
            $questionForm->addModule($module);
        }

        $em->persist($questionForm);
        $em->flush();

        return $this->redirectToRoute('app_module_details', [
            'id' => $this->moduleId
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(QuestionType::class, $this->getQuestion());
    }

    private function getQuestion(): ?Question 
    {
        if ($this->questionId) {
            return $this->questionRepository->find($this->questionId);
        }
        return null;
    }

    private function getModule(): Module 
    {
        return $this->moduleRepository->find($this->moduleId);
    }
}
