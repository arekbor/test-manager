<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\TestVerify;
use App\DataTable\Type\TestDataTableType;
use App\Entity\Module;
use App\Entity\Test;
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

    #[Route('/index')]
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

    #[Route('/create/{id}')]
    public function create(Module $module): Response
    {
        return $this->render('test/create.html.twig', [
            'module' => $module
        ]);
    }

    #[Route('/details/{id}')]
    public function details(Test $test): Response
    {
        return $this->render('test/details.html.twig', [
            'test' => $test
        ]);
    }

    #[Route('/delete/{id}')]
    public function delete(Test $test, EntityManagerInterface $em): Response
    {
        $em->remove($test);
        $em->flush();

        return $this->redirectToRoute('app_test_index');
    }

    #[Route('/notFound')]
    public function notFound(): Response 
    {
        return $this->render('/test/notFound.html.twig');
    }

    #[Route('/notValid')]
    public function notValid(): Response 
    {
        return $this->render('/test/notValid.html.twig');
    }

    #[Route('/solve/{id}')]
    #[TestVerify]
    public function solve(?Test $test): Response
    {
        return $this->render('/test/solve.html.twig', [
            'test' => $test
        ]);
    }
}