<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Video;
use App\Service\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/video')]
class VideoController extends BaseController
{
    public function __construct(
        private VideoService $videoService,
        private ValidatorInterface $validator,
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
    ): JsonResponse
    {
        $file = $request->files->get('file');

        $errors = $this->validator->validate($file, new File(extensions: ['mp4', 'mov']));
        if (count($errors) > 0) {
            $error = $errors->get(0)->getMessage();
            return $this->jsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        try {
            $video = $this->videoService->upload($file, $module);

            $this->em->persist($video);
            $this->em->flush();
        } catch(Exception) {
            return $this->jsonResponse($trans->trans('controller.video.response.upload_fail'), Response::HTTP_BAD_REQUEST);
        }

        return $this->jsonResponse($trans->trans('controller.video.response.upload_success'));
    }

    #[Route('/download/{id}')]
    public function download(Video $video): BinaryFileResponse
    {
        $file = $this->videoService->getVideoFile($video);

        return $this->file($file->getPath(), $file->getFilename());
    }

    #[Route('/watch/{id}')]
    public function watch(Video $video): BinaryFileResponse
    {
        $file = $this->videoService->getVideoFile($video);
        $binaryFileResponse = new BinaryFileResponse($file->getPath());
        $binaryFileResponse->headers->set('Content-Type', $file->getContentType());

        return $binaryFileResponse;
    }

    #[Route('/delete/{moduleId}/{videoId}')]
    public function delete(
        #[MapEntity(id: 'moduleId')] Module $module,
        #[MapEntity(id: 'videoId')] Video $video,
    ): Response
    {
        $this->videoService->deleteVideo($video);

        $this->em->remove($video);
        $this->em->flush();

        return $this->redirectToRoute('app_module_details', [
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

    private function jsonResponse(string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['message' => $message], $status);
    }
}
