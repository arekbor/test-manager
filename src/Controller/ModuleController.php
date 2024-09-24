<?php

namespace App\Controller;

use App\DataTable\Type\QuestionDataTableType;
use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\QuestionRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/module')]
class ModuleController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/create')]
    public function create(): Response
    {
        $form = $this->createForm(ModuleType::class);

        return $this->render('module/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/details/{id}')]
    public function details(
        Module $module, 
        Request $request,
        QuestionRepository $questionRepository
    ): Response
    {

        //TODO: zrob component z tworzeniem pytania
        //TODO: przerób data table na live component aby nie towryzc co chwile forma
        //TODO: zrób usuwanie pytań z modułu
        $form = $this->createForm(ModuleType::class, $module);

        $query = $questionRepository->findByModuleId($module->getId());

        $dataTable = $this->createDataTable(QuestionDataTableType::class, $query, [
            'module_id' => $module->getId()
        ]);
        $dataTable->handleRequest($request);

        return $this->render('module/details.html.twig', [
            'form' => $form,
            'questions' => $dataTable->createView()
        ]);
    }
}
