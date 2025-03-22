<?php 

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Entity\Module;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/question')]
class QuestionController extends AbstractController
{
    #[Route('/create/{moduleId}', name: 'app_question_create')]
    public function create(#[MapEntity(id: 'moduleId')] Module $module): Response
    {
        return $this->render('question/create.html.twig', [
            'module' => $module
        ]);
    }
    
    #[Route('/details/{moduleId}/{questionId}', name: 'app_question_details')]
    public function details(
        #[MapEntity(id: 'moduleId')] Module $module, 
        #[MapEntity(id: 'questionId')] Question $question): Response
    {
        return $this->render('question/details.html.twig', [
            'module' => $module, 
            'question' => $question
        ]);
    }

    #[Route('/delete/{moduleId}/{questionId}', name: 'app_question_delete')]
    public function delete(
        #[MapEntity(id: 'moduleId')] Module $module,
        #[MapEntity(id: 'questionId')] Question $question,
        EntityManagerInterface $em,
    ): Response
    {
        $em->remove($question);
        $em->flush();

        return $this->redirectToRoute('app_module_questions', [
            'id' => $module->getId()
        ]);
    }
}
