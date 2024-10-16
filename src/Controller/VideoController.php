<?php

namespace App\Controller;

use App\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/video')]
class VideoController extends AbstractController
{
    #[Route('/upload/{id}')]
    public function upload(Module $module): Response
    {
        return $this->render('video/upload.html.twig');
    }
}
