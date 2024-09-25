<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/question')]
class QuestionController extends AbstractController
{
    #[Route('/create/{moduleId}')]
    public function create(int $moduleId): Response
    {
        return $this->render('question/create.html.twig', [
            'moduleId' => $moduleId
        ]);
    }
    
    #[Route('/edit/{moduleId}/{questionId}')]
    public function edit(int $moduleId, int $questionId): Response
    {
        return $this->render('question/edit.html.twig', [
            'moduleId' => $moduleId,
            'questionId' => $questionId
        ]);
    }
}
