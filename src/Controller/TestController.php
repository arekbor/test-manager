<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/create/{id}')]
    public function create(Module $module): Response
    {
        return $this->render('test/create.html.twig', [
            'module' => $module
        ]);
    }
}