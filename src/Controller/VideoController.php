<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/video')]
class VideoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/upload/{id}')]
    public function upload(
        Request $request,
        Module $module,
        TranslatorInterface $trans,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $video = new Video();

        $file = $request->files->get('file');
        $video->setVideoFile($file);
        $video->addModule($module);

        $errors = $validator->validate($video);
        if (count($errors) > 0) {
            $error = $errors->get(0)->getMessage();

            $this->addFlash('danger', $error);
            
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($video);
        $this->em->flush();

        $this->addFlash('success', $trans->trans('flash.uploadFile.success'));
        return $this->json(null, Response::HTTP_OK);
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
