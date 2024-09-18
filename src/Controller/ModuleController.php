<?php

namespace App\Controller;

use App\Form\ModuleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/module')]
class ModuleController extends AbstractController
{
    #[Route('/create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModuleType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $module = $form->getData();
            $em->persist($module);
            $em->flush();

            return $this->redirectToRoute('app_home_index');
        }

        return $this->render('module/create.html.twig', [
            'form' => $form
        ]);
    }
}
