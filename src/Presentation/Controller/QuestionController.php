<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Question\Command\DeleteQuestion;
use App\Application\Question\Query\GetQuestionModel;
use App\Application\Shared\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use App\Application\Question\Model\QuestionModel;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/question')]
final class QuestionController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[Route('/create/{moduleId}', name: 'app_question_create')]
    public function create(Uuid $moduleId): Response
    {
        return $this->render('question/create.html.twig', [
            'moduleId' => $moduleId
        ]);
    }

    #[Route('/details/{moduleId}/{questionId}', name: 'app_question_details')]
    public function details(Uuid $moduleId, Uuid $questionId): Response
    {
        /**
         * @var QuestionModel $questionModel
         */
        $questionModel = $this->queryBus->query(new GetQuestionModel($questionId));

        return $this->render('question/details.html.twig', [
            'questionModel' => $questionModel,
            'questionId' => $questionId,
            'moduleId' => $moduleId
        ]);
    }

    #[Route('/delete/{moduleId}/{questionId}', name: 'app_question_delete')]
    public function delete(Uuid $moduleId, Uuid $questionId): Response
    {
        $response = $this->redirectToRoute('app_module_questions', [
            'id' => $moduleId
        ]);

        try {
            $this->commandBus->dispatch(new DeleteQuestion($questionId));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.questionController.delete.error'));

            return $response;
        }

        $this->addFlash('success', $this->trans->trans('flash.questionController.delete.success'));

        return $response;
    }

    #[Route('/import/{id}', name: 'app_question_import')]
    public function import(Uuid $id): Response
    {
        return $this->render('question/import.html.twig', [
            'moduleId' => $id
        ]);
    }
}
