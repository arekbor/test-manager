<?php 

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Question\Query\GetQuestionModel;
use App\Application\Shared\QueryBusInterface;
use App\Domain\Entity\Module;
use App\Domain\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use App\Application\Question\Model\QuestionModel;

#[Route('/question')]
class QuestionController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {
    }

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
