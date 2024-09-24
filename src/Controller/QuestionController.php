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
    #[Route('/create/{id}')]
    public function create(Module $module): Response
    {
        return $this->render('question/create.html.twig', [
            'module' => $module
        ]);
    }

    #[Route('/edit/{module_id}/{question_id}')]
    public function edit(
        #[MapEntity(id: 'module_id')] Module $module,
        #[MapEntity(id: 'question_id')] Question $question,
    ): Response
    {
        return $this->render('question/edit.html.twig', [
            'module' => $module,
            'question' => $question
        ]);
    }
}
