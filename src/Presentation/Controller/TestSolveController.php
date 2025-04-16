<?php 

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Shared\QueryBusInterface;
use App\Application\Test\Query\GetDataForTestSolve;
use App\Domain\Entity\Test;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Test\Model\DataForTestSolve;
use App\Application\Video\Query\GetVideoFile;
use App\Presentation\Attribute\TestVerify;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Uid\Uuid;

/**
 * All routes from this controller are open to public access!
 */
#[Route('/testSolve')]
final class TestSolveController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {
    }

    #[Route('/solve/{id}', name: 'app_testsolve_solve')]
    public function solve(Uuid $id): Response
    {
        try {
            /**
             * @var DataForTestSolve $dataForTestSolve
             */
            $dataForTestSolve = $this->queryBus->query(new GetDataForTestSolve($id));
        } catch (\Exception) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        return $this->render('/testSolve/solve.html.twig', [
            'dataForTestSolve' => $dataForTestSolve
        ]);
    }

    #[Route('/introduction/{id}', name: 'app_testsolve_introduction')]
    #[TestVerify]
    public function introduction(?Test $test): Response
    {
        return $this->render('/testSolve/introduction.html.twig', [
            'test' => $test
        ]);
    }

    #[Route('/conclusion', name: 'app_testsolve_conclusion')]
    public function conclusion(): Response
    {
        return $this->render('/testSolve/conclusion.html.twig');
    }

    #[Route('/clause', name: 'app_testsolve_clause')]
    public function clause(): Response
    {
        return $this->render('/testSolve/clause.html.twig');
    }

    #[Route('/video/{id}', name: 'app_testsolve_video')]
    public function video(Uuid $id): BinaryFileResponse
    {
        /**
         * @var \SplFileInfo $file
         */
        $file = $this->queryBus->query(new GetVideoFile($id));

        return $this->file($file);
    }

    #[Route('/notFound', name: 'app_testsolve_notfound')]
    public function notFound(): Response 
    {
        return $this->render('/testSolve/notFound.html.twig');
    }

    #[Route('/notValid', name: 'app_testsolve_notvalid')]
    public function notValid(): Response 
    {
        return $this->render('/testSolve/notValid.html.twig');
    }
}