<?php 

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Domain\Entity\Module;
use App\Domain\Entity\Test;
use App\Presentation\DataTable\Type\TestDataTableType;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/index', name: 'app_test_index')]
    public function index(
        Request $request, 
        TestRepository $testRepository
    ): Response
    {
        $query = $testRepository->findAllWithModules();
        $testDataTable = $this->createDataTable(TestDataTableType::class, $query);
        $testDataTable->handleRequest($request);

        return $this->render('test/index.html.twig', [
            'test_data_table' => $testDataTable->createView()
        ]);
    }

    #[Route('/create/{id}', name: 'app_test_create')]
    public function create(Module $module): Response
    {
        return $this->render('test/create.html.twig', [
            'module' => $module
        ]);
    }

    #[Route('/details/{id}', name: 'app_test_details')]
    public function details(Test $test): Response
    {
        return $this->render('test/details.html.twig', [
            'test' => $test
        ]);
    }

    #[Route('/delete/{id}', name: 'app_test_delete')]
    public function delete(Test $test, EntityManagerInterface $em): Response
    {
        $em->remove($test);
        $em->flush();

        return $this->redirectToRoute('app_test_index');
    }
}