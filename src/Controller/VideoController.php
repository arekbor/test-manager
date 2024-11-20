<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Video;
use App\Response\MessageResponse;
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
    public function upload(Module $module): Response
    {
        return $this->render('video/upload.html.twig', [
            'moduleId' => $module->getId()
        ]);
    }

    #[Route('/upload-video/{id}')]
    public function uploadVideo(
        Request $request,
        Module $module,
        TranslatorInterface $trans,
        ValidatorInterface $validator,
    ): JsonResponse
    {
        $file = $request->files->get('file');

        $video = new Video();
        $video->setVideoFile($file);
        $video->addModule($module);

        $errors = $validator->validate($video);
        if (count($errors) > 0) {
            $error = $errors->get(0)->getMessage();
            return MessageResponse::createResponse($error, Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($video);
        $this->em->flush();

        $successMessage = $trans->trans('controller.video.response.upload_success');
        return MessageResponse::createResponse($successMessage);
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
