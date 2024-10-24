<?php declare(strict_types=1);

namespace App\Controller;

use App\DataTable\Type\ModuleDataTableType;
use App\Repository\ModuleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseDataTableController
{
    #[Route('/home')]
    public function index(Request $request, ModuleRepository $moduleRepository): Response
    {
        $query = $moduleRepository->createQueryBuilder('m');

        return $this->render('home/index.html.twig', [
            'module_data_table_view' => $this->createDataTableView(ModuleDataTableType::class, $request, $query)
        ]);
    }
}
