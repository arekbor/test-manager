<?php

namespace App\Twig\Components;

use App\Entity\Module;
use App\Entity\Question;
use App\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
    public ?Question $initialFormData;

    #[LiveProp]
    public array $formOptions;

    #[LiveProp]
    public Module $module;

    #[LiveAction]
    public function save(EntityManagerInterface $em) 
    {
        $this->submitForm();

        $question = $this->getForm()->getData();

        if ($this->module) {
            $question->addModule($this->module);
        }

        $em->persist($question);
        $em->flush();

        return $this->redirectToRoute('app_module_details', [
            'id' => $this->module->getId()
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(QuestionType::class, $this->initialFormData, $this->formOptions);
    }
}
