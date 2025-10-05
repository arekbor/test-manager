<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\Component\Uid\Uuid;
use App\Application\Test\Model\TestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Presentation\DataTable\Type\TestDataTableType;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Test\Command\DeleteTest\DeleteTest;
use App\Application\Test\Query\GetTestModel\GetTestModel;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\Test\Query\GetTestViewModels\GetTestViewModels;
use App\Application\Test\Query\GetTestModelWithDefaultExpirationDate\GetTestModelWithDefaultExpirationDate;

#[Route('/test')]
final class TestController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[Route('/index', name: 'app_test_index')]
    public function index(Request $request): Response
    {
        $queryBuilder = $this->queryBus->ask(new GetTestViewModels());

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
        $testModel = $this->queryBus->ask(new GetTestModelWithDefaultExpirationDate());

        return $this->render('test/create.html.twig', [
            'moduleId' => $id,
            'testModel' => $testModel
        ]);
    }

    #[Route('/details/{id}/{moduleId}', name: 'app_test_details')]
    public function details(Uuid $id, Uuid $moduleId): Response
    {
        $testModel = $this->queryBus->ask(new GetTestModel($id));

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
            $this->commandBus->handle(new DeleteTest($id));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.testController.delete.error'));

            return $response;
        }

        $this->addFlash('success', $this->trans->trans('flash.testController.delete.success'));

        return $response;
    }
}
