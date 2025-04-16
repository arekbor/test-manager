<?php 

declare(strict_types = 1);

namespace App\Presentation\Controller;

use App\Application\Shared\QueryBusInterface;
use App\Application\Test\Command\DeleteTest;
use App\Application\Test\Query\GetTestModelWithDefaultExpirationDate;
use App\Presentation\DataTable\Type\TestDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use App\Application\Test\Model\TestModel;
use App\Application\Test\Query\GetTestModel;
use App\Application\Test\Query\GetTestViewModels;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/test')]
final class TestController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {
    }

    #[Route('/index', name: 'app_test_index')]
    public function index(Request $request): Response
    {
        $queryBuilder = $this->queryBus->query(new GetTestViewModels());

        $dataTable = $this->createDataTable(TestDataTableType::class, $queryBuilder);

        $dataTable->handleRequest($request);

        $dataTableView = $dataTable->createView();

        return $this->render('test/index.html.twig', [
            'test_data_table' => $dataTableView
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
    public function delete(Uuid $id): Response
    {
        $response = $this->redirectToRoute('app_test_index');

        try {
            $this->commandBus->dispatch(new DeleteTest($id));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.testController.delete.error'));

            return $response;
        }

        $this->addFlash('success', $this->trans->trans('flash.testController.delete.success'));

        return $response;
    }
}