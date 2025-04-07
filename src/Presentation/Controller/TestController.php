<?php 

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Shared\QueryBusInterface;
use App\Application\Test\Query\GetTestModelWithDefaultExpirationDate;
use App\Domain\Entity\Test;
use App\Presentation\DataTable\Type\TestDataTableType;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use App\Application\Test\Model\TestModel;
use App\Application\Test\Query\GetTestModel;

#[Route('/test')]
class TestController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {
    }

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
    public function create(Uuid $id): Response
    {
        /**
         * @var TestModel $testModel
         */
        $testModel = $this->queryBus->query(new GetTestModelWithDefaultExpirationDate());

        return $this->render('test/create.html.twig', [
            'moduleId' => $id,
            'testModel' => $testModel
        ]);
    }

    #[Route('/details/{id}/{moduleId}', name: 'app_test_details')]
    public function details(Uuid $id, Uuid $moduleId): Response
    {
        $testModel = $this->queryBus->query(new GetTestModel($id));

        return $this->render('test/details.html.twig', [
            'testModel' => $testModel,
            'testId' => $id,
            'moduleId' => $moduleId
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