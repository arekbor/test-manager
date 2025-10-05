<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Test\Model\DataForTestSolve;
use App\Application\Shared\Bus\QueryBusInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Application\Video\Query\GetVideoFile\GetVideoFile;
use App\Application\AppSetting\Model\TestMessageAppSetting;
use App\Application\AppSetting\Model\TestPrivacyPolicyAppSetting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\Test\Query\GetDataForTestSolve\GetDataForTestSolve;
use App\Application\Test\Query\GetTestMessageAppSetting\GetTestMessageAppSetting;
use App\Application\Test\Query\GetTestPrivacyPolicyAppSetting\GetTestPrivacyPolicyAppSetting;

/**
 * All routes from this controller are open to public access!
 */
#[Route('/testSolve')]
final class TestSolveController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {}

    #[Route('/solve/{id}', name: 'app_testsolve_solve')]
    public function solve(Uuid $id): Response
    {
        try {
            /**
             * @var DataForTestSolve $dataForTestSolve
             */
            $dataForTestSolve = $this->queryBus->ask(new GetDataForTestSolve($id));
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
            $testMessageAppSetting = $this->queryBus->ask(new GetTestMessageAppSetting($request->getLocale()));
        } catch (\Exception) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        return $this->render('/testSolve/message.html.twig', [
            'testMessageAppSetting' => $testMessageAppSetting,
            'type' => $type,
            'testId' => $id
        ]);
    }

    #[Route('/privacy', name: 'app_testsolve_privacy')]
    public function privacy(Request $request): Response
    {
        try {
            /**
             * @var TestPrivacyPolicyAppSetting|null $testPrivacyPolicyAppSetting
             */
            $testPrivacyPolicyAppSetting = $this->queryBus->ask(new GetTestPrivacyPolicyAppSetting($request->getLocale()));
        } catch (\Exception) {
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        return $this->render('/testSolve/privacy.html.twig', [
            'testPrivacyPolicyAppSetting' => $testPrivacyPolicyAppSetting
        ]);
    }

    #[Route('/video/{id}', name: 'app_testsolve_video')]
    public function video(Uuid $id): BinaryFileResponse
    {
        /**
         * @var \SplFileInfo $file
         */
        $file = $this->queryBus->ask(new GetVideoFile($id));

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
