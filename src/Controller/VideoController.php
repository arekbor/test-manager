<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\VideoType;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/video')]
class VideoController extends AbstractController
{
    private string $uploadDirectory;

    public function __construct(ParameterBagInterface $params) 
    {
        $this->uploadDirectory = $params->get('app.video_upload_directory');
    }

    #[Route('/upload/{id}')]
    public function upload(Request $request, Module $module): Response
    {
        $form = $this->createForm(VideoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if (!$file) {
                throw new NotFoundHttpException("File not found in form");
            }

            $this->processFile($file);

            return $this->redirectToRoute('app_module_details', [
                'id' => $module->getId()
            ]);
        }

        return $this->render('video/upload.html.twig', [
            'form' => $form
        ]);
    }

    private function processFile(File $file): void 
    {
        if (!is_dir($this->uploadDirectory) || !is_writable($this->uploadDirectory)) {
            throw new RuntimeException("Upload directory is not writable or does not exist.");
        }

        $newFilename = Uuid::v7() . '.' . $file->guessExtension();

        if (file_exists($this->uploadDirectory . '/' . $newFilename)) {
            throw new FileException("A file with this name already exists.");
        }

        try {
            $file->move($this->uploadDirectory, $newFilename);
        } catch (FileException $e) {
            throw new RuntimeException("An error occurred while uploading the file. Please try again later.");
        }
    }
}
