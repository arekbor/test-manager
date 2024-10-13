<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Question;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/question')]
class QuestionController extends AbstractController
{
    #[Route('/create/{moduleId}')]
    public function create(
        #[MapEntity(id: 'moduleId')] Module $module): Response
    {
        return $this->render('question/create.html.twig', [
            'module' => $module
        ]);
    }
    
    #[Route('/edit/{moduleId}/{questionId}')]
    public function edit(
        #[MapEntity(id: 'moduleId')] Module $module, 
        #[MapEntity(id: 'questionId')] Question $question): Response
    {
        return $this->render('question/edit.html.twig', [
            'module' => $module,
            'question' => $question
        ]);
    }
}
