<?php 

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Attribute\TestVerify;
use App\Domain\Entity\Test;
use App\Domain\Entity\Video;
use App\Handler\FileHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * All routes from this controller are open to public access!
 */
#[Route('/testSolve')]
class TestSolveController extends AbstractController
{
    #[Route('/solve/{id}', name: 'app_testsolve_solve')]
    #[TestVerify]
    public function solve(?Test $test): Response
    {
        return $this->render('/testSolve/solve.html.twig', [
            'test' => $test
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

    #[Route('/video/{testId}/{videoId}', name: 'app_testsolve_video')]
    #[TestVerify]
    public function video(
        #[MapEntity(id: 'testId')] Test $test,
        #[MapEntity(id: 'videoId')] Video $video,
        FileHandler $fileHandler,
    ): Response
    {
        if (!$test->videoBelongsToTest($video)) {
            throw new AccessDeniedHttpException();
        }   

        $file = $fileHandler->getFile($video, 'file');
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