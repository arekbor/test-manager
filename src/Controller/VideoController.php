<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Video;
use App\Repository\ModuleRepository;
use App\Service\VideoService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/video')]
class VideoController extends AbstractController
{
    public function __construct(
        private VideoService $videoService,
        private ValidatorInterface $validator,
        private ParameterBagInterface $params,
    ) {
    }

    #[Route('/upload/{id}')]
    public function upload(Module $module): Response
    {
        return $this->render('video/upload.html.twig', ['moduleId' => $module->getId()]);
    }

    #[Route('/upload-video')]
    public function uploadVideo(Request $request, ModuleRepository $moduleRepository): JsonResponse
    {
        $video = new Video();
        $module = $moduleRepository->find($request->get('moduleId'));
        if (!$module) {
            return $this->jsonResponse("Module not found", Response::HTTP_NOT_FOUND);
        }
        $video->addModule($module);
        $video->setFile($request->files->get('file'));

        $errors = $this->validator->validate($video);
        if (count($errors) > 0) {
            return $this->jsonResponse($errors->get(0)->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->videoService->upload($video);
        } catch(Exception) {
            return $this->jsonResponse("Error while uploading the video", Response::HTTP_BAD_REQUEST);
        }

        return $this->jsonResponse("File uploaded successfully");
    }

    private function jsonResponse(string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['message' => $message], $status);
    }
}
