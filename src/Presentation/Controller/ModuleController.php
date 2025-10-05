<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Component\Uid\Uuid;
use App\Application\Module\Model\ModuleModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Presentation\DataTable\Type\VideoDataTableType;
use App\Presentation\DataTable\Type\ModuleDataTableType;
use App\Presentation\DataTable\Type\QuestionDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use App\Application\Module\Query\GetModuleModel\GetModuleModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\Video\Query\GetVideoViewModels\GetVideoViewModels;
use App\Application\Module\Query\GetModuleViewModels\GetModuleViewModels;
use App\Application\Question\Query\GetQuestionViewModels\GetQuestionViewModels;

#[Route('/module')]
final class ModuleController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {}

    #[Route('/create', name: 'app_module_create')]
    public function create(): Response
    {
        return $this->render('module/create.html.twig');
    }

    #[Route('/details/general/{id}', name: 'app_module_general')]
    public function general(Uuid $id): Response
    {
        /**
         * @var ModuleModel $moduleModel
         */
        $moduleModel = $this->queryBus->ask(new GetModuleModel($id));

        return $this->render('module/general.html.twig', [
            'moduleId' => $id,
            'moduleModel' => $moduleModel,
        ]);
    }

    #[Route('/details/questions/{id}', name: 'app_module_questions')]
    public function questions(Uuid $id, Request $request): Response
    {
        $queryBuilder = $this->queryBus->ask(new GetQuestionViewModels($id));

        $dataTable = $this->createDataTable(QuestionDataTableType::class, $queryBuilder, [
            'module_id' => $id
        ]);

        $dataTable->handleRequest($request);

        $dataTableView = $dataTable->createView();

        return $this->render('module/questions.html.twig', [
            'moduleId' => $id,
            'question_data_table' => $dataTableView
        ]);
    }

    #[Route('/details/videos/{id}', name: 'app_module_videos')]
    public function videos(Uuid $id, Request $request): Response
    {
        $queryBuilder = $this->queryBus->ask(new GetVideoViewModels($id));

        $dataTable = $this->createDataTable(VideoDataTableType::class, $queryBuilder, [
            'module_id' => $id
        ]);

        $dataTable->handleRequest($request);

        $dataTableView = $dataTable->createView();

        return $this->render('module/videos.html.twig', [
            'moduleId' => $id,
            'video_data_table' => $dataTableView
        ]);
    }

    #[Route('/index', name: 'app_module_index')]
    public function index(Request $request): Response
    {
        $queryBuilder = $this->queryBus->ask(new GetModuleViewModels());

        $dataTable = $this->createDataTable(ModuleDataTableType::class, $queryBuilder);

        $dataTable->handleRequest($request);

        $dataTableView = $dataTable->createView();

        return $this->render('module/index.html.twig', [
            'module_data_table' => $dataTableView
        ]);
    }
}
