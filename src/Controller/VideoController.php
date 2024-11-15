<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Video;
use App\Repository\ModuleRepository;
use App\Service\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/video')]
class VideoController extends BaseController
{
    public function __construct(
        private VideoService $videoService,
        private ValidatorInterface $validator,
        private ParameterBagInterface $params,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/upload/{id}')]
    public function upload(Module $module): Response
    {
        return $this->render('video/upload.html.twig', ['moduleId' => $module->getId()]);
    }

    #[Route('/details/{moduleId}/{videoId}')]
    public function details(
        #[MapEntity(id: 'moduleId')] Module $module,
        #[MapEntity(id: 'videoId')] Video $video
    ): Response
    {
        $file = $this->videoService->getFile($video);
        $video->setFile($file);

        return $this->render('video/details.html.twig', ['moduleId' => $module->getId(), 'video' => $video]);
    }

    #[Route('/upload-video')]
    public function uploadVideo(
        Request $request, 
        ModuleRepository $moduleRepository,
        TranslatorInterface $trans,
    ): JsonResponse
    {
        $video = new Video();
        $module = $moduleRepository->find($request->get('moduleId'));
        if (!$module) {
            return $this->jsonResponse($trans->trans('controller.video.response.module_not_found'), Response::HTTP_NOT_FOUND);
        }
        $video->addModule($module);
        $video->setFile($request->files->get('file'));

        $errors = $this->validator->validate($video);
        if (count($errors) > 0) {
            return $this->jsonResponse($errors->get(0)->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->videoService->uploadFile($video);

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
        $file = $this->videoService->getFile($video);
        return $this->file($file->getPathname(), $file->getFilename());
    }

    #[Route('/delete/{moduleId}/{videoId}')]
    public function delete(
        #[MapEntity(id: 'moduleId')] Module $module,
        #[MapEntity(id: 'videoId')] Video $video,
    ): Response
    {
        $this->videoService->deleteFile($video);

        $this->em->remove($video);
        $this->em->flush();

        return $this->redirectToRoute('app_module_details', [
            'id' => $module->getId()
        ]);
    }

    #[Route('/watch/{id}')]
    public function watch(Video $video): BinaryFileResponse
    {
        $file = $this->videoService->getFile($video);
        $response = new BinaryFileResponse($file->getPathname());
        $response->headers->set('Content-Type', $file->getMimeType());

        return $response;
    }

    private function jsonResponse(string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['message' => $message], $status);
    }
}
