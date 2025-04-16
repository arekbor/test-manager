<?php 

declare(strict_types = 1);

namespace App\Presentation\Controller;

use App\Application\AppSetting\Model\TestMessageAppSetting;
use App\Application\AppSetting\Model\TestClauseAppSetting;
use App\Application\Shared\QueryBusInterface;
use App\Application\Test\Query\GetDataForTestSolve;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Test\Model\DataForTestSolve;
use App\Application\Test\Query\GetTestClauseAppSetting;
use App\Application\Test\Query\GetTestMessageAppSetting;
use App\Application\Video\Query\GetVideoFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/message/{type}/{id}', name: 'app_testsolve_message')]
    public function introduction(Uuid $id, string $type, Request $request): Response
    {
        try {
            /**
             * @var TestMessageAppSetting|null $testMessageAppSetting
             */
            $testMessageAppSetting = $this->queryBus->query(new GetTestMessageAppSetting($request->getLocale()));
        } catch (\Exception) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        return $this->render('/testSolve/introduction.html.twig', [
            'testMessageAppSetting' => $testMessageAppSetting,
            'type' => $type,
            'testId' => $id
        ]);
    }

    #[Route('/clause', name: 'app_testsolve_clause')]
    public function clause(Request $request): Response
    {
        try {
            /**
             * @var TestClauseAppSetting|null $testClauseAppSetting
             */
            $testClauseAppSetting = $this->queryBus->query(new GetTestClauseAppSetting($request->getLocale()));
        } catch (\Exception) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        return $this->render('/testSolve/clause.html.twig', [
            'testClauseAppSetting' => $testClauseAppSetting
        ]);
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