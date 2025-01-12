<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\IgnoreLocaleSession;
use App\Attribute\TestVerify;
use App\Entity\Test;
use App\Entity\Video;
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
    #[Route('/notFound')]
    #[IgnoreLocaleSession]
    public function notFound(): Response 
    {
        return $this->render('/testSolve/notFound.html.twig');
    }

    #[Route('/notValid')]
    #[IgnoreLocaleSession]
    public function notValid(): Response 
    {
        return $this->render('/testSolve/notValid.html.twig');
    }

    #[Route('/video/{testId}/{videoId}')]
    #[IgnoreLocaleSession]
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

        $file = $fileHandler->getFile($video, 'videoFile');
        return $this->file($file);
    }

    #[Route('/introduction/{id}')]
    #[IgnoreLocaleSession]
    #[TestVerify]
    public function introduction(?Test $test): Response
    {
        return $this->render('/testSolve/introduction.html.twig', [
            'test' => $test
        ]);
    }

    #[Route('/conclusion/{id}')]
    #[IgnoreLocaleSession]
    #[TestVerify]
    public function conclusion(?Test $test): Response
    {
        return $this->render('/testSolve/conclusion.html.twig', [
            'test' => $test
        ]);
    }
}