<?php declare(strict_types=1);

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
    public ?Question $questionProp = null;

    #[LiveProp]
    public Module $moduleProp;

    public function __construct(
        private QuestionRepository $questionRepository, 
        private ModuleRepository $moduleRepository
    ) {
    }

    #[LiveAction]
    public function submit(EntityManagerInterface $em): Response
    {
        $this->submitForm();
        $question = $this->getForm()->getData();

        if ($this->moduleProp) {
            $question->addModule($this->moduleProp);
        }

        $em->persist($question);
        $em->flush();

        return $this->redirectToRoute('app_module_questions', [
            'id' => $this->moduleProp->getId()
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(QuestionType::class, $this->questionProp);
    }
}
