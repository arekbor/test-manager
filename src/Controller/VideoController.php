<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/video')]
class VideoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/download/{id}')]
    public function download(
        Video $video, 
        DownloadHandler $downloadHandler
    ): StreamedResponse
    {
        return $downloadHandler->downloadObject($video, 'videoFile');
    }

    #[Route('/delete/{moduleId}/{videoId}')]
    public function delete(
        #[MapEntity(id: 'moduleId')] Module $module,
        #[MapEntity(id: 'videoId')] Video $video,
    ): Response
    {
        $this->em->remove($video);
        $this->em->flush();

        return $this->redirectToRoute('app_module_videos', [
            'id' => $module->getId()
        ]);
    }

    #[Route('/details/{moduleId}/{videoId}')]
    public function details(
        #[MapEntity(id: 'moduleId')] Module $module,
        #[MapEntity(id: 'videoId')] Video $video
    ): Response
    {
        $moduleId = $module->getId();

        return $this->render('video/details.html.twig', [
            'moduleId' => $moduleId, 
            'video' => $video
        ]);
    }
}
