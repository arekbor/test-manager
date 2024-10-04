<?php

namespace App\Controller;

use App\DataTable\Type\ModuleDataTableType;
use App\Repository\ModuleRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    //TODO: naprawd niewidoczne ERROR w spanach przy bledach
    use DataTableFactoryAwareTrait;

    #[Route('/home')]
    public function index(
        Request $request, 
        ModuleRepository $moduleRepository
    ): Response
    {
        $query = $moduleRepository->createQueryBuilder('m');

        $dataTable = $this->createDataTable(ModuleDataTableType::class, $query);
        $dataTable->handleRequest($request);

        return $this->render('home/index.html.twig', [
            'modules' => $dataTable->createView()
        ]);
    }
}
