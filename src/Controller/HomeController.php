<?php declare(strict_types=1);

namespace App\Controller;

use App\DataTable\Type\ModuleDataTableType;
use App\Repository\ModuleRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
    use DataTableFactoryAwareTrait;

    #[Route('/home')]
    public function index(
        ModuleRepository $moduleRepository
    ): Response
    {
        $query = $moduleRepository->createQueryBuilder('m');
        $moduleDataTable = $this->createDataTable(ModuleDataTableType::class, $query); 

        return $this->render('home/index.html.twig', [
            'module_data_table' => $moduleDataTable->createView()
        ]);
    }
}
