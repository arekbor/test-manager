<?php

namespace App\Controller;

use App\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/video')]
class VideoController extends AbstractController
{
    #[Route('/upload/{id}')]
    public function upload(Module $module): Response
    {
        return $this->render('video/upload.html.twig', [
            'moduleId' => $module->getId()
        ]);
    }

    //TODO: this function is not finished
    #[Route('/upload-video')]
    public function uploadVideo(Request $request): JsonResponse
    {
        $moduleId = $request->get('moduleId');
        $file = $request->files->get('file');

        if (!$file || !$moduleId) {
            return new JsonResponse([
                'message' => 'File or moduleId not found in form'
            ], Response::HTTP_NOT_FOUND);
        }

        sleep(10);

        return new JsonResponse([
            'message' => 'File uploaded successfully'
        ], Response::HTTP_OK);
    }
}
